<?php

namespace Modules\EIS\Services\Product;

class ProductTransformer
{
    public function fromEis(array $item): array
    {
        return [
            'name'  => $item['name'],
            'sku'   => $item['sku'],
            'price' => $item['price'] ?? 0,
            'cost'  => $item['cost'] ?? 0,
            'stock' => $item['stock'] ?? 0,
        ];
    }
}