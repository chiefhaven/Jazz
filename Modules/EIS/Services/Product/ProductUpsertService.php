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

        if ($map) {
            $product = AppProduct::with('variations')->find($map->product_id);
        } else {
            $product = new AppProduct();
            $product->business_id = $businessId;
        }

        $product->name = $item['name'];
        $product->sku = $item['sku'];
        $product->variations->default_sell_price = $item['price'];
        $product->cost = $item['cost'];
        $product->quantity = $item['stock'];
        $product->save();

        EisProductMap::updateOrCreate(
            [
                'business_id' => $businessId,
                'product_id'  => $product->id,
            ],
            [
                'eis_product_id' => $eisId,
                'sku' => $item['sku'],
                'last_synced_at' => now(),
            ]
        );

        return $product;
    }
}