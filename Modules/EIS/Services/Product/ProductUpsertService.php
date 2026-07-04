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
        // PRODUCT LEVEL
        // -----------------------
        $product->business_id = $businessId;
        $product->name = $item['name'] ?? null;
        $product->sku = $item['sku'] ?? null;
        $product->created_by = 10000000; // TODO: Automated by system user
        $product->save();

        // -----------------------
        // VARIATION LEVEL
        // -----------------------
        $variation = $product->variations()->first();

        if (!$variation) {
            $variation = $product->variations()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'default_sell_price' => $item['price'] ?? 0,
                'default_purchase_price' => $item['cost'] ?? 0,
                'profit_percent' => $this->profit($item),
                'created_by' => 10000000, // TODO: Automated by system user
            ]);
        } else {
            $variation->update([
                'default_sell_price' => $item['price'] ?? 0,
                'default_purchase_price' => $item['cost'] ?? 0,
                'profit_percent' => $this->profit($item),
            ]);
        }

        // -----------------------
        // STOCK (LOCATION SAFE)
        // -----------------------
        $locationId = $this->getDefaultLocation($businessId);

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
        // MAPPING TABLE
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