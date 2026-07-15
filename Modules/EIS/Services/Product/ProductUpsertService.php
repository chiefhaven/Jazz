<?php

namespace Modules\EIS\Services\Product;

use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisProductMap;

class ProductUpsertService
{
    /**
     * Upsert a product from EIS data.
     *
     * @param int $businessId
     * @param array $item
     * @param string $eisId
     * @return Product
     * @throws \Exception
     */
    public function upsert(int $businessId, array $item, string $eisId)
    {
        Log::info('ProductUpsertService started', [
            'business_id' => $businessId,
            'eis_product_id' => $eisId,
            'product_sku' => $item['sku'] ?? null
        ]);

        return DB::transaction(function () use ($businessId, $item, $eisId) {
            // -----------------------
            // MAP CHECK
            // -----------------------
            $map = EisProductMap::withTrashed()
                ->where('business_id', $businessId)
                ->where('eis_product_id', $eisId)
                ->first();

            if ($map && $map->trashed()) {
                $map->restore();
                $map->refresh();
                Log::info('Restored soft-deleted EIS product map', [
                    'business_id' => $businessId,
                    'eis_product_id' => $eisId,
                    'product_id' => $map->product_id
                ]);
            }

            $product = null;

            if ($map) {
                Log::debug('Found existing EIS product map', [
                    'business_id' => $businessId,
                    'product_id' => $map->product_id
                ]);

                $product = Product::withTrashed()
                    ->with(['variations.variation_location_details'])
                    ->find($map->product_id);

                if ($product && $product->trashed()) {
                    $product->restore();
                    $product->refresh();
                    $product->load([
                        'variations.variation_location_details'
                    ]);
                    Log::info('Restored soft-deleted product', [
                        'business_id' => $businessId,
                        'product_id' => $product->id
                    ]);
                }
            } else {
                // Look for an existing product by SKU, including deleted ones
                if (!empty($item['sku'])) {
                    $product = Product::withTrashed()
                        ->with(['variations.variation_location_details'])
                        ->where('business_id', $businessId)
                        ->where('sku', $item['sku'])
                        ->first();

                    Log::debug('Searching for product by SKU', [
                        'business_id' => $businessId,
                        'sku' => $item['sku'],
                        'found' => !empty($product)
                    ]);
                } else {
                    $product = null;
                }

                if ($product) {
                    if ($product->trashed()) {
                        $product->restore();
                        $product->refresh();
                        Log::info('Restored soft-deleted product by SKU', [
                            'business_id' => $businessId,
                            'product_id' => $product->id,
                            'sku' => $product->sku
                        ]);
                    }

                    EisProductMap::updateOrCreate(
                        [
                            'business_id' => $businessId,
                            'eis_product_id' => $eisId,
                        ],
                        [
                            'product_id' => $product->id,
                            'sku' => $product->sku,
                            'last_synced_at' => now(),
                        ]
                    );

                    Log::info('Created EIS product map for existing product', [
                        'business_id' => $businessId,
                        'eis_product_id' => $eisId,
                        'product_id' => $product->id
                    ]);
                } else {
                    $product = new Product();
                    Log::debug('Creating new product', [
                        'business_id' => $businessId,
                        'eis_product_id' => $eisId
                    ]);
                }
            }

            // -----------------------
            // PRODUCT
            // -----------------------
            $isNew = !$product->exists;

            $product->business_id = $businessId;

            // Only set these fields when creating a new product
            if ($isNew) {
                $product->type = 'single';
                $product->created_by = $this->getSystemUserId($businessId);
                $product->enable_stock = false;
                $product->expiry_period_type = null;
                Log::info('Creating new product', [
                    'business_id' => $businessId,
                    'product_name' => $item['name'] ?? null
                ]);
            }

            // Update only EIS-controlled fields
            $product->name = $item['name'] ?? $product->name;
            $product->sku = $item['sku'] ?? $product->sku;
            $product->eis_product_id = $eisId;
            $product->eis_last_synced_at = now();

            $unitId = $this->getUnitId(
                $businessId,
                $item['unit_of_measure'] ?? null
            );

            if ($unitId) {
                $product->unit_id = $unitId;
            }

            $product->expiry_period = $item['expiry_period'] ?? $product->expiry_period;

            $product->save();

            Log::debug('Product saved', [
                'business_id' => $businessId,
                'product_id' => $product->id,
                'is_new' => $isNew
            ]);

            // -----------------------
            // PRODUCT VARIATION
            // -----------------------
            $productVariationId = DB::table('product_variations')
                ->where('product_id', $product->id)
                ->value('id');

            if (!$productVariationId) {
                $productVariationId = DB::table('product_variations')
                    ->insertGetId([
                        'variation_template_id' => null,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'is_dummy' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                Log::debug('Created product variation', [
                    'product_id' => $product->id,
                    'product_variation_id' => $productVariationId
                ]);
            }

            // -----------------------
            // VARIATION
            // -----------------------
            $variation = $product->variations()
                ->where('product_variation_id', $productVariationId)
                ->first();

            if (!$variation) {
                $variation = $product->variations()->create([
                    'product_variation_id' => $productVariationId,
                    'name' => $product->name,
                    'default_sell_price' => 0,
                    'default_purchase_price' => 0,
                    'sell_price_inc_tax' => 0,
                    'profit_percent' => 0,
                ]);

                Log::debug('Created variation', [
                    'product_id' => $product->id,
                    'variation_id' => $variation->id
                ]);
            }

            $profitPercent = $this->calculateProfit($item);

            $variation->update([
                'default_sell_price' => $item['price'] ?? 0,
                'default_purchase_price' => $item['cost'] ?? 0,
                'sell_price_inc_tax' => $item['price'] ?? 0,
                'sub_sku' => $item['sku'] ?? null,
                'profit_percent' => $profitPercent,
                'product_variation_id' => $productVariationId,
            ]);

            Log::debug('Updated variation', [
                'product_id' => $product->id,
                'variation_id' => $variation->id,
                'price' => $item['price'] ?? 0,
                'cost' => $item['cost'] ?? 0
            ]);

            // -----------------------
            // LOCATION (FROM EIS SITE ID)
            // -----------------------
            $locationId = $this->getLocationFromSite(
                $businessId,
                $item['site_id'] ?? null
            );

            if (!$locationId) {
                $errorMessage = "Business location not found for EIS siteId: " . ($item['site_id'] ?? 'NULL');
                Log::error($errorMessage, [
                    'business_id' => $businessId,
                    'site_id' => $item['site_id'] ?? null
                ]);
                throw new \Exception($errorMessage);
            }

            // -----------------------
    // PRODUCT LOCATION
    // -----------------------
    DB::table('product_locations')->updateOrInsert(
        [
            'product_id' => $product->id,
            'location_id' => $locationId,
        ],
        [
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    // -----------------------
    // VARIATION LOCATION DETAILS
    // -----------------------
    DB::table('variation_location_details')->updateOrInsert(
        [
            'variation_id' => $variation->id,
            'location_id' => $locationId,
        ],
        [
            'qty_available' => $item['stock'] ?? 0,
            'product_id' => $product->id,
            'product_variation_id' => $productVariationId,
            'updated_at' => now(),
            'created_at' => now(),
        ]
    );

    // -----------------------
    // EIS PRODUCT MAP
    // -----------------------
    EisProductMap::updateOrCreate(
        [
            'business_id' => $businessId,
            'eis_product_id' => $eisId,
        ],
        [
            'product_id' => $product->id,
            'sku' => $item['sku'] ?? null,
            'last_synced_at' => now(),
        ]
    );

    Log::info('Product upsert completed successfully', [
        'business_id' => $businessId,
        'product_id' => $product->id,
        'eis_product_id' => $eisId,
        'sku' => $product->sku
    ]);

    return $product;
});
    }

    /**
     * Calculate profit percentage.
     *
     * @param array $item
     * @return float
     */
    private function calculateProfit(array $item): float
    {
        $price = $item['price'] ?? 0;
        $cost = $item['cost'] ?? 0;

        if ($price <= 0) {
            return 0;
        }

        return (($price - $cost) / $price) * 100;
    }

    /**
     * Get system user ID.
     *
     * @param int $businessId
     * @return int
     */
    private function getSystemUserId(int $businessId): int
    {
        // Try to get the first admin user for this business
        $userId = DB::table('user_business')
            ->where('business_id', $businessId)
            ->where('is_admin', 1)
            ->value('user_id');

        if (!$userId) {
            // Fallback to the first user
            $userId = DB::table('user_business')
                ->where('business_id', $businessId)
                ->value('user_id');
        }

        return $userId ?? 1;
    }

    /**
     * Get location from EIS site ID.
     *
     * @param int $businessId
     * @param string|null $siteId
     * @return int|null
     */
    private function getLocationFromSite(int $businessId, ?string $siteId): ?int
    {
        if (empty($siteId)) {
            return null;
        }

        return DB::table('business_locations')
            ->where('business_id', $businessId)
            ->where('eis_site_id', $siteId)
            ->value('id');
    }

    /**
     * Get unit ID from unit name.
     *
     * @param int $businessId
     * @param string|null $unitName
     * @return int|null
     */
    private function getUnitId(int $businessId, ?string $unitName): ?int
    {
        if (empty($unitName)) {
            return null;
        }

        return DB::table('units')
            ->where('business_id', $businessId)
            ->where(function ($query) use ($unitName) {
                $query->where('short_name', $unitName)
                    ->orWhere('actual_name', $unitName);
            })
            ->value('id');
    }
}