<?php

namespace Modules\EIS\Services\Products;

use Modules\EIS\Models\EisProductMap;
use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            'product_sku' => $item['sku'] ?? null,
            'product_name' => $item['name'] ?? null
        ]);

        return DB::transaction(function () use ($businessId, $item, $eisId) {
            Log::debug('Transaction started for product upsert', [
                'business_id' => $businessId,
                'eis_product_id' => $eisId
            ]);

            // -----------------------
            // MAP CHECK
            // -----------------------
            Log::debug('Checking for existing EIS product map', [
                'business_id' => $businessId,
                'eis_product_id' => $eisId
            ]);

            $map = EisProductMap::withTrashed()
                ->where('business_id', $businessId)
                ->where('eis_product_id', $eisId)
                ->first();

            if ($map?->trashed()) {
                Log::info('Restoring soft-deleted EIS product map', [
                    'business_id' => $businessId,
                    'eis_product_id' => $eisId,
                    'product_id' => $map->product_id,
                    'deleted_at' => $map->deleted_at
                ]);

                $map->restore();
                $map->refresh();

                Log::info('EIS product map restored successfully', [
                    'business_id' => $businessId,
                    'eis_product_id' => $eisId,
                    'product_id' => $map->product_id
                ]);
            }

            $product = null;

            if ($map) {
                Log::info('Found existing EIS product map', [
                    'business_id' => $businessId,
                    'eis_product_id' => $eisId,
                    'product_id' => $map->product_id
                ]);

                $product = Product::withTrashed()
                    ->with(['variations.variation_location_details'])
                    ->find($map->product_id);

                if ($product?->trashed()) {
                    Log::info('Product is soft-deleted, restoring', [
                        'business_id' => $businessId,
                        'product_id' => $product->id,
                        'deleted_at' => $product->deleted_at
                    ]);

                    $product->restore();
                    $product->refresh();

                    $product->load([
                        'variations.variation_location_details'
                    ]);

                    Log::info('Product restored successfully', [
                        'business_id' => $businessId,
                        'product_id' => $product->id
                    ]);
                }

                Log::debug('Product found from map', [
                    'business_id' => $businessId,
                    'product_id' => $product->id ?? null,
                    'product_name' => $product->name ?? null,
                    'product_sku' => $product->sku ?? null
                ]);
            } else {
                Log::debug('No EIS product map found, searching by SKU', [
                    'business_id' => $businessId,
                    'sku' => $item['sku'] ?? null
                ]);

                // Look for an existing product by SKU, including deleted ones
                if (!empty($item['sku'])) {
                    $product = Product::withTrashed()
                        ->with(['variations.variation_location_details'])
                        ->where('business_id', $businessId)
                        ->where('sku', $item['sku'])
                        ->first();

                    Log::debug('Product search by SKU completed', [
                        'business_id' => $businessId,
                        'sku' => $item['sku'],
                        'found' => !empty($product)
                    ]);
                } else {
                    Log::debug('No SKU provided, skipping SKU search');
                    $product = null;
                }

                if ($product) {
                    Log::info('Found existing product by SKU', [
                        'business_id' => $businessId,
                        'product_id' => $product->id,
                        'sku' => $product->sku,
                        'is_trashed' => $product->trashed()
                    ]);

                    if ($product->trashed()) {
                        Log::info('Product is soft-deleted, restoring', [
                            'business_id' => $businessId,
                            'product_id' => $product->id,
                            'deleted_at' => $product->deleted_at
                        ]);

                        $product->restore();
                        $product->refresh();

                        Log::info('Product restored successfully', [
                            'business_id' => $businessId,
                            'product_id' => $product->id
                        ]);
                    }

                    Log::info('Creating EIS product map for existing product', [
                        'business_id' => $businessId,
                        'eis_product_id' => $eisId,
                        'product_id' => $product->id
                    ]);

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

                    Log::info('EIS product map created successfully', [
                        'business_id' => $businessId,
                        'eis_product_id' => $eisId,
                        'product_id' => $product->id
                    ]);
                } else {
                    Log::info('No existing product found, creating new product', [
                        'business_id' => $businessId,
                        'eis_product_id' => $eisId,
                        'product_name' => $item['name'] ?? 'Unknown',
                        'product_sku' => $item['sku'] ?? 'Unknown'
                    ]);

                    $product = new Product();

                    Log::debug('New Product instance created', [
                        'business_id' => $businessId,
                        'eis_product_id' => $eisId
                    ]);
                }
            }

            // -----------------------
            // PRODUCT
            // -----------------------
            $isNew = !$product->exists;

            Log::info('Processing product data', [
                'business_id' => $businessId,
                'product_id' => $product->id ?? 'new',
                'is_new' => $isNew,
                'product_name' => $item['name'] ?? null,
                'product_sku' => $item['sku'] ?? null
            ]);

            $product->business_id = $businessId;

            // Only set these fields when creating a new product
            if ($isNew) {
                Log::debug('Setting default values for new product', [
                    'business_id' => $businessId,
                    'product_id' => $product->id ?? 'new'
                });

                $product->type = 'single';
                $product->created_by = 10000000;
                $product->enable_stock = false;
                $product->expiry_period_type = null;
            }

            // Update only EIS-controlled fields
            $product->name = $item['name'] ?? $product->name;
            $product->sku = $item['sku'] ?? $product->sku;
            $product->eis_product_id = $eisId;
            $product->eis_last_synced_at = now();

            Log::debug('Product fields set', [
                'business_id' => $businessId,
                'product_id' => $product->id ?? 'new',
                'name' => $product->name,
                'sku' => $product->sku,
                'eis_product_id' => $product->eis_product_id
            ]);

            $unitId = $this->getUnitId(
                $businessId,
                $item['unit_of_measure'] ?? null
            );

            if ($unitId) {
                Log::debug('Unit found for product', [
                    'business_id' => $businessId,
                    'unit_id' => $unitId,
                    'unit_name' => $item['unit_of_measure']
                ]);
                $product->unit_id = $unitId;
            } else {
                Log::debug('No unit found for product', [
                    'business_id' => $businessId,
                    'unit_name' => $item['unit_of_measure'] ?? null
                ]);
            }

            $product->expiry_period = $item['expiry_period'] ?? $product->expiry_period;

            $product->save();

            Log::info('Product saved successfully', [
                'business_id' => $businessId,
                'product_id' => $product->id,
                'is_new' => $isNew,
                'name' => $product->name,
                'sku' => $product->sku
            ]);

            // -----------------------
            // PRODUCT VARIATION
            // -----------------------
            Log::debug('Checking for product variation', [
                'business_id' => $businessId,
                'product_id' => $product->id
            ]);

            $productVariationId = DB::table('product_variations')
                ->where('product_id', $product->id)
                ->value('id');

            if (!$productVariationId) {
                Log::info('Creating product variation', [
                    'business_id' => $businessId,
                    'product_id' => $product->id,
                    'product_name' => $product->name
                ]);

                $productVariationId = DB::table('product_variations')
                    ->insertGetId([
                        'variation_template_id' => null,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'is_dummy' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                Log::info('Product variation created', [
                    'business_id' => $businessId,
                    'product_id' => $product->id,
                    'product_variation_id' => $productVariationId
                ]);
            } else {
                Log::debug('Product variation found', [
                    'business_id' => $businessId,
                    'product_id' => $product->id,
                    'product_variation_id' => $productVariationId
                ]);
            }

            // -----------------------
            // VARIATION
            // -----------------------
            Log::debug('Checking for variation', [
                'business_id' => $businessId,
                'product_id' => $product->id,
                'product_variation_id' => $productVariationId
            ]);

            $variation = $product->variations()
                ->where('product_variation_id', $productVariationId)
                ->first();

            if (!$variation) {
                Log::info('Creating variation', [
                    'business_id' => $businessId,
                    'product_id' => $product->id,
                    'product_variation_id' => $productVariationId
                ]);

                $variation = $product->variations()->create([
                    'product_variation_id' => $productVariationId,
                    'name' => $product->name,
                    'default_sell_price' => 0,
                    'default_purchase_price' => 0,
                    'sell_price_inc_tax' => 0,
                    'profit_percent' => 0,
                ]);

                Log::info('Variation created', [
                    'business_id' => $businessId,
                    'product_id' => $product->id,
                    'variation_id' => $variation->id
                ]);
            } else {
                Log::debug('Variation found', [
                    'business_id' => $businessId,
                    'product_id' => $product->id,
                    'variation_id' => $variation->id
                ]);
            }

            $profitPercent = $this->profit($item);

            Log::debug('Updating variation with pricing data', [
                'business_id' => $businessId,
                'variation_id' => $variation->id,
                'price' => $item['price'] ?? 0,
                'cost' => $item['cost'] ?? 0,
                'profit_percent' => $profitPercent,
                'sub_sku' => $item['sku'] ?? null
            ]);

            $variation->update([
                'default_sell_price' => $item['price'] ?? 0,
                'default_purchase_price' => $item['cost'] ?? 0,
                'sell_price_inc_tax' => $item['price'] ?? 0,
                'sub_sku' => $item['sku'] ?? null,
                'profit_percent' => $profitPercent,
                'product_variation_id' => $productVariationId,
            ]);

            Log::info('Variation updated successfully', [
                'business_id' => $businessId,
                'variation_id' => $variation->id,
                'price' => $item['price'] ?? 0,
                'cost' => $item['cost'] ?? 0
            ]);

            // -----------------------
            // LOCATION (FROM EIS SITE ID)
            // -----------------------
            Log::debug('Looking for location from EIS site ID', [
                'business_id' => $businessId,
                'site_id' => $item['site_id'] ?? null
            ]);

            $locationId = $this->getLocationFromSite(
                $businessId,
                $item['site_id'] ?? null
            );

            if (!$locationId) {
                Log::error('Business location not found for EIS siteId', [
                    'business_id' => $businessId,
                    'site_id' => $item['site_id'] ?? 'NULL'
                ]);

                throw new \Exception(
                    "Business location not found for EIS siteId: " . ($item['site_id'] ?? 'NULL')
                );
            }

            Log::debug('Location found', [
                'business_id' => $businessId,
                'location_id' => $locationId,
                'site_id' => $item['site_id'] ?? null
            ]);

            // -----------------------
            // PRODUCT LOCATION
            // -----------------------
            Log::debug('Updating product location', [
                'business_id' => $businessId,
                'product_id' => $product->id,
                'location_id' => $locationId
            ]);

            DB::table('product_locations')->updateOrInsert(
                [
                    'product_id' => $product->id,
                    'location_id' => $locationId,
                ]
            );

            Log::debug('Product location updated', [
                'business_id' => $businessId,
                'product_id' => $product->id,
                'location_id' => $locationId
            ]);

            // -----------------------
            // VARIATION LOCATION DETAILS
            // -----------------------
            Log::debug('Updating variation location details', [
                'business_id' => $businessId,
                'variation_id' => $variation->id,
                'location_id' => $locationId,
                'qty_available' => $item['stock'] ?? 0
            ]);

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

            Log::debug('Variation location details updated', [
                'business_id' => $businessId,
                'variation_id' => $variation->id,
                'location_id' => $locationId,
                'qty_available' => $item['stock'] ?? 0
            ]);

            // -----------------------
            // EIS PRODUCT MAP
            // -----------------------
            Log::debug('Updating EIS product map', [
                'business_id' => $businessId,
                'eis_product_id' => $eisId,
                'product_id' => $product->id,
                'sku' => $item['sku'] ?? null
            ]);

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

            Log::info('EIS product map updated successfully', [
                'business_id' => $businessId,
                'eis_product_id' => $eisId,
                'product_id' => $product->id
            ]);

            Log::info('Product upsert completed successfully', [
                'business_id' => $businessId,
                'product_id' => $product->id,
                'eis_product_id' => $eisId,
                'sku' => $product->sku,
                'name' => $product->name
            ]);

            return $product;
        });
    }

    // -----------------------
    // PROFIT
    // -----------------------
    private function profit(array $item): float
    {
        $price = $item['price'] ?? 0;
        $cost  = $item['cost'] ?? 0;

        $profitPercent = $price > 0
            ? (($price - $cost) / $price) * 100
            : 0;

        Log::debug('Profit calculated', [
            'price' => $price,
            'cost' => $cost,
            'profit_percent' => $profitPercent
        ]);

        return $profitPercent;
    }

    // -----------------------
    // LOCATION LOOKUP
    // -----------------------
    private function getLocationFromSite(int $businessId, ?string $siteId): ?int
    {
        if (empty($siteId)) {
            Log::debug('Empty site ID provided for location lookup', [
                'business_id' => $businessId
            ]);
            return null;
        }

        $locationId = DB::table('business_locations')
            ->where('business_id', $businessId)
            ->where('eis_site_id', $siteId)
            ->value('id');

        Log::debug('Location lookup result', [
            'business_id' => $businessId,
            'site_id' => $siteId,
            'location_id' => $locationId
        ]);

        return $locationId;
    }

    // -----------------------
    // UNIT OF MEASURE LOOKUP
    // -----------------------
    private function getUnitId(int $businessId, ?string $unitName): ?int
    {
        if (empty($unitName)) {
            Log::debug('Empty unit name provided for unit lookup', [
                'business_id' => $businessId
            ]);
            return null;
        }

        $unitId = DB::table('units')
            ->where('business_id', $businessId)
            ->where(function ($q) use ($unitName) {
                $q->where('short_name', $unitName)
                ->orWhere('actual_name', $unitName);
            })
            ->value('id');

        Log::debug('Unit lookup result', [
            'business_id' => $businessId,
            'unit_name' => $unitName,
            'unit_id' => $unitId
        ]);

        return $unitId;
    }
}