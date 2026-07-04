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
            $product->created_by = 10000000;
            $product->save();

            // -----------------------
            // PRODUCT VARIATION (SAFE UPSERT)
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
            // VARIATION (UPSERT SAFE)
            // -----------------------
            $variation = $product->variations()
                ->firstOrCreate(
                    ['product_id' => $product->id],
                    [
                        'name' => $product->name,
                        'product_variation_id' => $productVariationId,
                        'default_sell_price' => 0,
                        'default_purchase_price' => 0,
                        'profit_percent' => 0,
                    ]
                );

            $variation->update([
                'default_sell_price' => $item['price'] ?? 0,
                'default_purchase_price' => $item['cost'] ?? 0,
                'profit_percent' => $this->profit($item),
                'product_variation_id' => $productVariationId,
            ]);

            // -----------------------
            // LOCATION
            // -----------------------
            $locationId = $this->getDefaultLocation($businessId);

            if (!$locationId) {
                throw new \Exception("Missing business location for business_id {$businessId}");
            }

            // -----------------------
            // VLD (UPSERT STYLE)
            // -----------------------
            DB::table('variation_location_details')
                ->updateOrInsert(
                    [
                        'variation_id' => $variation->id,
                        'location_id' => $locationId,
                    ],
                    [
                        'qty_available' => $item['stock'] ?? 0,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

            // -----------------------
            // MAPPING
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

        return $price > 0 ? (($price - $cost) / $price) * 100 : 0;
    }

    // -----------------------
    // LOCATION
    // -----------------------
    private function getDefaultLocation(int $businessId)
    {
        return DB::table('business_locations')
            ->where('business_id', $businessId)
            ->value('id');
    }
}