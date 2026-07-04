<?php

namespace Modules\EIS\Services\Product;

use Modules\EIS\Models\EisProductMap;
use App\Models\Product;
use App\Product as AppProduct;

class ProductUpsertService
{
    public function upsert(int $businessId, array $item, string $eisId)
    {
        $map = EisProductMap::where('business_id', $businessId)
            ->where('eis_product_id', $eisId)
            ->first();

        $product = $map
            ? AppProduct::with(['variations.variation_location_details'])
                ->find($map->product_id)
            : new AppProduct();

        // -----------------------
        // PRODUCT
        // -----------------------
        $product->business_id = $businessId;
        $product->name = $item['name'] ?? null;
        $product->sku = $item['sku'] ?? null;
        $product->created_by = 10000000;
        $product->save();

        // -----------------------
        // PRODUCT VARIATION (NEW - REQUIRED)
        // -----------------------
        $productVariation = \DB::table('product_variations')
            ->where('product_id', $product->id)
            ->first();

        if (!$productVariation) {
            $productVariationId = \DB::table('product_variations')->insertGetId([
                'product_id' => $product->id,
                'name' => $product->name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $productVariationId = $productVariation->id;
        }

        // -----------------------
        // VARIATION
        // -----------------------
        $variation = $product->variations()->first();

        if (!$variation) {
            $variation = $product->variations()->create([
                'product_id' => $product->id,
                'product_variation_id' => $productVariationId, // 🔥 IMPORTANT FIX
                'name' => $product->name,
                'default_sell_price' => $item['price'] ?? 0,
                'default_purchase_price' => $item['cost'] ?? 0,
                'profit_percent' => $this->profit($item),
            ]);
        } else {
            $variation->update([
                'default_sell_price' => $item['price'] ?? 0,
                'default_purchase_price' => $item['cost'] ?? 0,
                'profit_percent' => $this->profit($item),
                'product_variation_id' => $productVariationId,
            ]);
        }

        // -----------------------
        // STOCK (VLD)
        // -----------------------
        $locationId = $this->getDefaultLocation($businessId);

        if (!$locationId) {
            throw new \Exception("Missing business location for business_id {$businessId}");
        }

        $vld = $variation->variation_location_details()
            ->where('location_id', $locationId)
            ->first();

        if (!$vld) {
            $variation->variation_location_details()->create([
                'location_id' => $locationId,
                'qty_available' => $item['stock'] ?? 0,
            ]);
        } else {
            $vld->update([
                'qty_available' => $item['stock'] ?? 0,
            ]);
        }

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
    }

    // -----------------------
    // PROFIT CALCULATION
    // -----------------------
    private function profit(array $item): float
    {
        $price = $item['price'] ?? 0;
        $cost  = $item['cost'] ?? 0;

        if ($price <= 0) {
            return 0;
        }

        return (($price - $cost) / $price) * 100;
    }

    // -----------------------
    // DEFAULT LOCATION
    // -----------------------
    private function getDefaultLocation(int $businessId)
    {
        return \DB::table('business_locations')
            ->where('business_id', $businessId)
            ->value('id');
    }
}