<?php

namespace Modules\EIS\Services\Product;

use Modules\EIS\Models\EisProductMap;
use App\Product;
use Illuminate\Support\Facades\DB;

class ProductUpsertService
{
    public function upsert(int $businessId, array $item, string $eisId)
    {
        return DB::transaction(function () use ($businessId, $item, $eisId) {

            // -----------------------
            // MAP CHECK
            // -----------------------
            $map = EisProductMap::withTrashed()
                ->where('business_id', $businessId)
                ->where('eis_product_id', $eisId)
                ->first();

            if ($map?->trashed()) {
                $map->restore();
                $map->refresh();
            }

            $product = null;

            if ($map) {
                $product = Product::withTrashed()
                    ->with(['variations.variation_location_details'])
                    ->find($map->product_id);

                if ($product?->trashed()) {
                    $product->restore();

                    $product->load([
                        'variations.variation_location_details'
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

                } else {
                    $product = null;
                }

                if ($product) {

                    if ($product->trashed()) {
                        $product->restore();
                        $product->refresh();
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
                } else {
                    $product = new Product();
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
                $product->created_by = 10000000;
                $product->enable_stock = false;
                $product->expiry_period_type = null;
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
            }

            $variation->update([
                'default_sell_price' => $item['price'] ?? 0,
                'default_purchase_price' => $item['cost'] ?? 0,
                'sell_price_inc_tax' => $item['price'] ?? 0,
                'sub_sku' => $item['sku'] ?? null,
                'profit_percent' => $this->profit($item),
                'product_variation_id' => $productVariationId,
            ]);

            // -----------------------
            // LOCATION (FROM EIS SITE ID)
            // -----------------------
            $locationId = $this->getLocationFromSite(
                $businessId,
                $item['site_id'] ?? null
            );

            if (!$locationId) {
                throw new \Exception(
                    "Business location not found for EIS siteId: " . ($item['site_id'] ?? 'NULL')
                );
            }

            // -----------------------
            // PRODUCT LOCATION
            // -----------------------
            DB::table('product_locations')->updateOrInsert(
                [
                    'product_id' => $product->id,
                    'location_id' => $locationId,
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

        return $price > 0
            ? (($price - $cost) / $price) * 100
            : 0;
    }

    // -----------------------
    // LOCATION LOOKUP
    // -----------------------
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

    // -----------------------
    // UNIT OF MEASURE LOOKUP
    // -----------------------
    private function getUnitId(int $businessId, ?string $unitName): ?int
    {
        if (empty($unitName)) {
            return null;
        }

        return DB::table('units')
            ->where('business_id', $businessId)
            ->where(function ($q) use ($unitName) {
                $q->where('short_name', $unitName)
                ->orWhere('actual_name', $unitName);
            })
            ->value('id');
    }
}