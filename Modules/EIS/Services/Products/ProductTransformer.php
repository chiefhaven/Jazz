<?php

namespace Modules\EIS\Services\Products;

use Illuminate\Support\Facades\Log;

class ProductTransformer
{
    /**
     * Transform EIS product data to internal format.
     *
     * @param array $item
     * @param string $eisId
     * @return array
     */
    public function fromEis(array $item, string $eisId): array
    {
        Log::debug('Transforming EIS product data', [
            'eis_id' => $eisId,
            'product_code' => $item['productCode'] ?? null,
            'product_name' => $item['productName'] ?? null
        ]);

        $transformed = [
            'name' => $this->extractName($item),
            'sku' => $this->extractSku($item, $eisId),
            'type' => $this->extractType($item),
            'price' => $this->extractPrice($item),
            'cost' => $this->extractCost($item),
            'stock' => $this->extractStock($item),
            'manage_stock' => $this->extractManageStock($item),
            'unit_of_measure' => $this->extractUnitOfMeasure($item),
            'expiry_period' => $this->extractExpiryPeriod($item),
            'site_id' => $this->extractSiteId($item),
            'description' => $this->extractDescription($item),
            'category' => $this->extractCategory($item),
            'brand' => $this->extractBrand($item),
            'tax_rate_id' => $this->extractTaxRateId($item),
            'is_active' => $this->extractIsActive($item),
        ];

        Log::debug('Product transformation completed', [
            'eis_id' => $eisId,
            'transformed_name' => $transformed['name'],
            'transformed_sku' => $transformed['sku'],
            'transformed_price' => $transformed['price'],
            'transformed_stock' => $transformed['stock'],
            'description' => $transformed['description']
        ]);

        return $transformed;
    }

    /**
     * Transform multiple EIS products.
     *
     * @param array $items
     * @return array
     */
    public function fromEisCollection(array $items): array
    {
        Log::info('Transforming EIS product collection', [
            'count' => count($items)
        ]);

        $transformed = [];

        foreach ($items as $item) {
            $eisId = $item['id'] ?? $item['productId'] ?? null;
            if ($eisId) {
                $transformed[] = $this->fromEis($item, $eisId);
            } else {
                Log::warning('EIS product missing ID', [
                    'item' => $item
                ]);
            }
        }

        Log::debug('EIS product collection transformation completed', [
            'total' => count($items),
            'transformed' => count($transformed)
        ]);

        return $transformed;
    }

    /**
     * Extract product name.
     *
     * @param array $item
     * @return string
     */
    private function extractName(array $item): string
    {
        $name = $item['productName'] ?? $item['name'] ?? $item['description'] ?? 'Unnamed Product';

        if (empty(trim($name))) {
            Log::warning('Product name is empty, using default', [
                'item' => $item
            ]);
            $name = 'Unnamed Product';
        }

        return trim($name);
    }

    /**
     * Extract product SKU.
     *
     * @param array $item
     * @param string $eisId
     * @return string
     */
    private function extractSku(array $item, string $eisId): string
    {
        $sku = $item['productCode'] ?? $item['sku'] ?? $item['code'] ?? null;

        if (empty($sku)) {
            Log::warning('Product SKU is empty, generating from EIS ID', [
                'eis_id' => $eisId
            ]);
            $sku = 'EIS-' . strtoupper($eisId);
        }

        return trim($sku);
    }

    /**
     * Extract product type.
     *
     * @param array $item
     * @return string
     */
    private function extractType(array $item): string
    {
        $type = $item['type'] ?? $item['productType'] ?? 'single';

        $allowedTypes = ['single', 'variable', 'combo', 'service'];

        if (!in_array($type, $allowedTypes)) {
            Log::warning('Invalid product type, defaulting to single', [
                'type' => $type
            ]);
            $type = 'single';
        }

        return $type;
    }

    /**
     * Extract product price.
     *
     * @param array $item
     * @return float
     */
    private function extractPrice(array $item): float
    {
        $price = (float) ($item['price'] ?? $item['sellingPrice'] ?? $item['unitPrice'] ?? 0);

        if ($price < 0) {
            Log::warning('Product price is negative, setting to 0', [
                'price' => $price
            ]);
            $price = 0;
        }

        return $price;
    }

    /**
     * Extract product cost.
     *
     * @param array $item
     * @return float
     */
    private function extractCost(array $item): float
    {
        $cost = (float) ($item['cost'] ?? $item['purchasePrice'] ?? $item['buyingPrice'] ?? 0);

        if ($cost < 0) {
            Log::warning('Product cost is negative, setting to 0', [
                'cost' => $cost
            ]);
            $cost = 0;
        }

        return $cost;
    }

    /**
     * Extract product stock.
     *
     * @param array $item
     * @return float
     */
    private function extractStock(array $item): float
    {
        $stock = (float) ($item['quantity'] ?? $item['stock'] ?? $item['stockQuantity'] ?? 0);

        if ($stock < 0) {
            Log::warning('Product stock is negative, setting to 0', [
                'stock' => $stock
            ]);
            $stock = 0;
        }

        return $stock;
    }

    /**
     * Extract manage stock flag.
     *
     * @param array $item
     * @return bool
     */
    private function extractManageStock(array $item): bool
    {
        $isProduct = $item['isProduct'] ?? $item['isStockable'] ?? true;

        return filter_var($isProduct, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Extract unit of measure.
     *
     * @param array $item
     * @return string|null
     */
    private function extractUnitOfMeasure(array $item): ?string
    {
        $unit = $item['unitOfMeasure'] ?? $item['unit'] ?? $item['uom'] ?? null;

        if ($unit) {
            $unit = trim($unit);
        }

        return $unit;
    }

    /**
     * Extract expiry period.
     *
     * @param array $item
     * @return string|null
     */
    private function extractExpiryPeriod(array $item): ?string
    {
        $expiry = $item['productExpiryDate'] ?? $item['expiryDate'] ?? $item['expiry'] ?? null;

        if ($expiry) {
            $expiry = trim($expiry);
        }

        return $expiry;
    }

    /**
     * Extract site ID.
     *
     * @param array $item
     * @return string|null
     */
    private function extractSiteId(array $item): ?string
    {
        $siteId = $item['siteId'] ?? $item['site_id'] ?? $item['locationId'] ?? null;

        if ($siteId) {
            $siteId = trim($siteId);
        }

        return $siteId;
    }

    /**
     * Extract product description.
     *
     * @param array $item
     * @return string|null
     */
    private function extractDescription(array $item): ?string
    {
        $description = $item['description'];

        if ($description) {
            $description = trim($description);
        }

        return $description;
    }

    /**
     * Extract product category.
     *
     * @param array $item
     * @return string|null
     */
    private function extractCategory(array $item): ?string
    {
        $category = $item['category'] ?? $item['productCategory'] ?? $item['categoryName'] ?? null;

        if ($category) {
            $category = trim($category);
        }

        return $category;
    }

    /**
     * Extract product brand.
     *
     * @param array $item
     * @return string|null
     */
    private function extractBrand(array $item): ?string
    {
        $brand = $item['brand'] ?? $item['productBrand'] ?? $item['brandName'] ?? null;

        if ($brand) {
            $brand = trim($brand);
        }

        return $brand;
    }

    /**
     * Extract tax rate ID.
     *
     * @param array $item
     * @return string|null
     */
    private function extractTaxRateId(array $item): ?string
    {
        $taxRateId = $item['taxRateId'] ?? $item['tax_id'] ?? $item['taxCode'] ?? null;

        if ($taxRateId) {
            $taxRateId = trim($taxRateId);
        }

        return $taxRateId;
    }

    /**
     * Extract is active flag.
     *
     * @param array $item
     * @return bool
     */
    private function extractIsActive(array $item): bool
    {
        $isActive = $item['isActive'] ?? $item['status'] ?? true;

        return filter_var($isActive, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Transform product to EIS format.
     *
     * @param array $product
     * @return array
     */
    public function toEis(array $product): array
    {
        Log::debug('Transforming product to EIS format', [
            'product_sku' => $product['sku'] ?? null
        ]);

        return [
            'productCode' => $product['sku'] ?? null,
            'productName' => $product['name'] ?? null,
            'productDescription' => $product['product_description'] ?? null,
            'price' => (float) ($product['price'] ?? 0),
            'cost' => (float) ($product['cost'] ?? 0),
            'quantity' => (float) ($product['stock'] ?? 0),
            'isProduct' => $product['manage_stock'] ?? true,
            'unitOfMeasure' => $product['unit_of_measure'] ?? null,
            'productExpiryDate' => $product['expiry_period'] ?? null,
            'siteId' => $product['site_id'] ?? null,
            'category' => $product['category'] ?? null,
            'brand' => $product['brand'] ?? null,
            'taxRateId' => $product['tax_rate_id'] ?? null,
            'isActive' => $product['is_active'] ?? true,
        ];
    }

    /**
     * Transform multiple products to EIS format.
     *
     * @param array $products
     * @return array
     */
    public function toEisCollection(array $products): array
    {
        Log::info('Transforming product collection to EIS format', [
            'count' => count($products)
        ]);

        $transformed = [];

        foreach ($products as $product) {
            $transformed[] = $this->toEis($product);
        }

        return $transformed;
    }

    /**
     * Validate transformed data.
     *
     * @param array $data
     * @return bool
     */
    public function validate(array $data): bool
    {
        if (empty($data['name'])) {
            Log::warning('Validation failed: Product name is required', [
                'data' => $data
            ]);
            return false;
        }

        if (empty($data['sku'])) {
            Log::warning('Validation failed: Product SKU is required', [
                'data' => $data
            ]);
            return false;
        }

        if ($data['price'] < 0) {
            Log::warning('Validation failed: Product price cannot be negative', [
                'price' => $data['price']
            ]);
            return false;
        }

        if ($data['cost'] < 0) {
            Log::warning('Validation failed: Product cost cannot be negative', [
                'cost' => $data['cost']
            ]);
            return false;
        }

        if ($data['stock'] < 0) {
            Log::warning('Validation failed: Product stock cannot be negative', [
                'stock' => $data['stock']
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get field mapping for documentation.
     *
     * @return array
     */
    public function getFieldMapping(): array
    {
        return [
            'EIS Field' => 'Internal Field',
            'productName' => 'name',
            'productCode' => 'sku',
            'price' => 'price',
            'cost' => 'cost',
            'quantity' => 'stock',
            'isProduct' => 'manage_stock',
            'unitOfMeasure' => 'unit_of_measure',
            'productExpiryDate' => 'expiry_period',
            'siteId' => 'site_id',
            'description' => 'description',
            'category' => 'category',
            'brand' => 'brand',
            'taxRateId' => 'tax_rate_id',
            'isActive' => 'is_active',
        ];
    }
}