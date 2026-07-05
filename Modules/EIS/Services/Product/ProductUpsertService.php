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
            $map = EisProductMap::where('business_id', $businessId)
                ->where('eis_product_id', $eisId)
                ->first();

            $product = $map
                ? Product::with(['variations.variation_location_details'])
                    ->find($map->product_id)
                : new Product();

            // -----------------------
            // PRODUCT
            // -----------------------
            $product->business_id = $businessId;

            $product->name = $item['name']
                ?? $item['productName']
                ?? $item['sku']
                ?? 'UNKNOWN PRODUCT';

            $product->sku = $item['sku'] ?? null;
            $product->unit_id = $this->getUnitId(
                $businessId,
                $item['unit_of_measure'] ?? null
            ) ?? 2; // default PCS
            $product->type = 'single';
            $product->expiry_period = $item['expiry_period'] ?? null;
            $product->expiry_period_type = null;
            $product->enable_stock = $item['manage_stock'] ?? false;
            $product->created_by = 10000000;
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
            $variation = $product->variations()->firstOrCreate(
                ['product_id' => $product->id],
                [
                    'name' => $product->name,
                    'product_variation_id' => $productVariationId,
                    'default_sell_price' => 0,
                    'default_purchase_price' => 0,
                    'sell_price_inc_tax' => 0,
                    'profit_percent' => 0,
                ]
            );

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