<?php

namespace Modules\EIS\Services\Products;

use App\Product;
use App\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisProductMap;

class ProductUpsertService {
    /**
     * Cache for unit lookups
     */
    private array $unitCache = [];
    
    /**
     * Cache for location lookups
     */
    private array $locationCache = [];
    
    /**
     * Cache for tax rate lookups
     */
    private array $taxRateCache = [];

    /**
     * Sync EIS product into UltimatePOS.
     */
    public function upsert(
        int $businessId,
        array $item,
        string $eisId
    ): Product {

        return DB::transaction(function () use (
            $businessId,
            $item,
            $eisId
        ) {

            Log::info('EIS product sync started', [
                'business_id' => $businessId,
                'eis_product_id' => $eisId,
                'sku' => $item['sku'] ?? null,
                'eis_tax_rate_id' => $item['tax_rate_id'] ?? null
            ]);

            /*
            |--------------------------------------------------------------------------
            | FIND EXISTING EIS MAP
            |--------------------------------------------------------------------------
            */
            $map = EisProductMap::withTrashed()
                ->where('business_id', $businessId)
                ->where('eis_product_id', $eisId)
                ->first();

            $product = null;

            /*
            |--------------------------------------------------------------------------
            | RESTORE EXISTING MAP
            |--------------------------------------------------------------------------
            */
            if ($map) {
                if ($map->trashed()) {
                    Log::info('Restoring deleted EIS map', [
                        'map_id' => $map->id,
                        'eis_product_id' => $eisId,
                    ]);
                    $map->restore();
                }

                /*
                |--------------------------------------------------------------------------
                | LOAD PRODUCT FROM MAP
                |--------------------------------------------------------------------------
                */
                $product = Product::withTrashed()
                    ->where('id', $map->product_id)
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | PRODUCT WAS PERMANENTLY DELETED
                |--------------------------------------------------------------------------
                */
                if (!$product) {
                    Log::warning('Mapped product missing, creating new product', [
                        'old_product_id' => $map->product_id,
                        'eis_product_id' => $eisId,
                    ]);
                    $product = new Product();
                }

                /*
                |--------------------------------------------------------------------------
                | RESTORE SOFT DELETED PRODUCT
                |--------------------------------------------------------------------------
                */
                if ($product->exists && $product->trashed()) {
                    Log::info('Restoring deleted product', [
                        'product_id' => $product->id,
                    ]);
                    $product->restore();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | FIND PRODUCT BY SKU (PRIMARY FALLBACK)
            |--------------------------------------------------------------------------
            */
            if (!$product || !$product->exists) {
                $sku = $item['sku'] ?? null;
                
                if ($sku) {
                    $product = Product::withTrashed()
                        ->where('business_id', $businessId)
                        ->where('sku', $sku)
                        ->first();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | FIND PRODUCT BY NAME (SECONDARY FALLBACK)
            |--------------------------------------------------------------------------
            */
            if (!$product || !$product->exists) {
                $name = $item['name'] ?? $item['productName'] ?? null;
                
                if ($name) {
                    $product = Product::withTrashed()
                        ->where('business_id', $businessId)
                        ->where('name', $name)
                        ->first();
                    
                    if ($product) {
                        Log::info('Product found by name fallback', [
                            'name' => $name,
                            'product_id' => $product->id,
                            'sku' => $product->sku
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | FIND PRODUCT BY EIS ID IN PRODUCT TABLE (FINAL FALLBACK)
            |--------------------------------------------------------------------------
            */
            if (!$product || !$product->exists) {
                $product = Product::withTrashed()
                    ->where('business_id', $businessId)
                    ->where('eis_product_id', $eisId)
                    ->first();
                
                if ($product) {
                    Log::info('Product found by EIS ID fallback', [
                        'eis_id' => $eisId,
                        'product_id' => $product->id
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CREATENEW PRODUCT
            |--------------------------------------------------------------------------
            */
            if (!$product) {
                Log::info('Creating new product', [
                    'eis_product_id' => $eisId,
                    'sku' => $item['sku'] ?? null,
                ]);
                $product = new Product();
            }

            if ($product && $product->trashed()) {
                $product->restore();
            }

            /*
            |--------------------------------------------------------------------------
            | PRODUCTDATA
            |--------------------------------------------------------------------------
            */
            $isNew = !$product->exists;

            $product->business_id = $businessId;

            if ($isNew) {
                $product->type = $item['type'] ?? 'single';
                $product->created_by = config('eis.system_user_id', 1);
                $product->enable_stock = $item['manage_stock'] ?? false;
            }

            /*
            |--------------------------------------------------------------------------
            | EIS CONTROLLED FIELDS WITH DUAL FALLBACKS
            |--------------------------------------------------------------------------
            */
            $product->name = $this->getProductName($item, $product, $eisId);
            $product->product_description = $this->getProductDescription($item, $product);
            $product->sku = $this->getProductSku($item, $product, $eisId);
            $product->tax = $this->getProductTaxRateId($item['tax_rate_id'], $businessId);
            $product->eis_product_id = $eisId;
            $product->eis_last_synced_at = now();

            /*
            |--------------------------------------------------------------------------
            | UNIT MAPPING WITH FALLBACKS
            |--------------------------------------------------------------------------
            */
            $unitId = $this->getUnitId(
                $businessId,
                $item['unit_of_measure'] ?? $item['unit'] ?? null
            );

            if ($unitId) {
                $product->unit_id = $unitId;
            } else {
                // Fallback: Try to find default unit
                $defaultUnit = DB::table('units')
                    ->where('business_id', $businessId)
                    ->where('short_name', 'pcs')
                    ->orWhere('short_name', 'pc')
                    ->first();
                
                if ($defaultUnit) {
                    $product->unit_id = $defaultUnit->id;
                    Log::warning('Using default unit as fallback', [
                        'unit_id' => $defaultUnit->id,
                        'unit_name' => $defaultUnit->short_name
                    ]);
                } else {
                    $anyUnit = DB::table('units')
                        ->where('business_id', $businessId)
                        ->first();
                    
                    if ($anyUnit) {
                        $product->unit_id = $anyUnit->id;
                        Log::warning('Using any available unit as final fallback', [
                            'unit_id' => $anyUnit->id
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | EXPIRY WITH FALLBACKS
            |--------------------------------------------------------------------------
            */
            $product->expiry_period = $this->getExpiryPeriod($item, $product);

            $product->save();

            Log::info('Product saved', [
                'product_id' => $product->id,
                'sku' => $product->sku,
                'new' => $isNew,
            ]);

            /*
            |--------------------------------------------------------------------------
            | CREATE/UPDATE EIS MAP
            |--------------------------------------------------------------------------
            */
            $this->saveProductMap(
                $businessId,
                $eisId,
                $product,
                $item['sku'] ?? null
            );

            /*
            |--------------------------------------------------------------------------
            | CONTINUE WITH:
            | - Product variation
            | - Variation
            | - Location
            | - Stock
            |--------------------------------------------------------------------------
            */
            return $this->syncVariationAndLocation(
                $businessId,
                $product,
                $item
            );
        });
    }

    /**
     * Sync variation and stock details.
     */
    private function syncVariationAndLocation(
        int $businessId,
        Product $product,
        array $item
    ): Product {

        /*
        |--------------------------------------------------------------------------
        | PRODUCT VARIATION
        |--------------------------------------------------------------------------
        */
        $productVariationId = DB::table('product_variations')
            ->where('product_id', $product->id)
            ->lockForUpdate()
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

            Log::info('Product variation created', [
                'product_id' => $product->id,
                'variation_id' => $productVariationId
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VARIATION
        |--------------------------------------------------------------------------
        */
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

            Log::info('Variation created', [
                'variation_id' => $variation->id
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GET TAX RATE FROM SHORT NAME
        |--------------------------------------------------------------------------
        */
        $taxRate = $this->getTaxRateByShortName($businessId, $item);
        $taxPercentage = $taxRate ? (float) $taxRate->rate : 0;

        /*
        |--------------------------------------------------------------------------
        | UPDATEPRICES WITH TAX CALCULATION
        |--------------------------------------------------------------------------
        */
        $sellPrice = $this->getSellPrice($item, $variation);
        $cost = $this->getCostPrice($item, $variation);
        
        // Calculate sell price excluding tax
        $sellPriceExclTax = $taxPercentage > 0 
            ? $sellPrice / (1 + ($taxPercentage / 100))
            : $sellPrice;

        $defaultPurchasePriceIncTax = $taxPercentage > 0 
            ? $cost / (1 - ($taxPercentage / 100))
            : $cost;

        // Calculate tax amount
        $taxAmount = $sellPrice - $sellPriceExclTax;

        Log::debug('Price calculation with tax', [
            'tax_percentage' => $taxPercentage,
            'eis_tax_rate_id' => $item['tax_rate_id'] ?? null,
            'tax_rate_id' => $taxRate->id ?? null,
            'ddp_inc_tax' => $defaultPurchasePriceIncTax,
            'sell_price_incl_tax' => $sellPrice,
            'sell_price_excl_tax' => $sellPriceExclTax,
            'tax_amount' => $taxAmount
        ]);

        $variation->update([
            'default_sell_price' => $sellPriceExclTax, // Price excluding tax
            'default_purchase_price' => $cost,
            'dpp_inc_tax' => $defaultPurchasePriceIncTax,
            'sell_price_inc_tax' => $sellPrice, // Price including tax
            'sub_sku' => $item['sku'] ?? $product->sku,
            'profit_percent' => $this->profit($sellPriceExclTax, $cost, $defaultPurchasePriceIncTax),
        ]);

        /*
        |--------------------------------------------------------------------------
        | LOCATION WITH FALLBACKS
        |--------------------------------------------------------------------------
        */
        $locationId = $this->getLocationWithFallbacks($businessId, $item);

        if (!$locationId) {
            Log::error('No location found for product', [
                'product_id' => $product->id,
                'business_id' => $businessId,
                'site_id' => $item['site_id'] ?? null
            ]);
            throw new \Exception(
                'EIS location not mapped: ' . ($item['site_id'] ?? 'NULL')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCT LOCATION
        |--------------------------------------------------------------------------
        */
        DB::table('product_locations')->updateOrInsert(
            [
                'product_id' => $product->id,
                'location_id' => $locationId,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | STOCK LOCATION
        |--------------------------------------------------------------------------
        */
        $stock = $this->getStockWithFallbacks($item);
        
        DB::table('variation_location_details')->updateOrInsert(
            [
                'variation_id' => $variation->id,
                'location_id' => $locationId,
            ],
            [
                'product_id' => $product->id,
                'product_variation_id' => $productVariationId,
                'qty_available' => $stock,
                'updated_at' => now(),
            ]
        );

        Log::info('Product inventory synced', [
            'product_id' => $product->id,
            'variation_id' => $variation->id,
            'location_id' => $locationId,
            'dpp_inc_tax' => $defaultPurchasePriceIncTax,
            'stock' => $stock,
            'tax_percentage' => $taxPercentage,
            'eis_tax_rate_id' => $item['tax_rate_id'] ?? null,
            'price_excl_tax' => $sellPriceExclTax,
            'price_incl_tax' => $sellPrice,
        ]);

        return $product;
    }

    /**
     * Get tax rate by short name from EIS data.
     *
     * @param int $businessId
     * @param array $item
     * @return object|null
     */
    private function getTaxRateByShortName(int $businessId, array $item): ?object
    {
        $shortName = $item['tax_rate_id'] ?? null;
        
        Log::debug('Looking for tax rate by short name', [
            'business_id' => $businessId,
            'short_name' => $shortName
        ]);

        // Check cache first
        $cacheKey = $businessId . ':' . ($shortName ?? 'default');
        if (isset($this->taxRateCache[$cacheKey])) {
            Log::debug('Tax rate found in cache', [
                'short_name' => $shortName,
                'rate' => $this->taxRateCache[$cacheKey]->rate ?? null
            ]);
            return $this->taxRateCache[$cacheKey];
        }

        $taxRate = null;

        // Primary: Find by short name (case insensitive)
        if (!empty($shortName)) {
            $taxRate = DB::table('tax_rates')
                ->where('business_id', $businessId)
                ->whereRaw('LOWER(tax_rate_id) = ?', [strtolower(trim($shortName))])
                ->first();
            
            if ($taxRate) {
                Log::debug('Tax rate found by short name', [
                    'short_name' => $shortName,
                    'tax_rate_id' => $taxRate->id,
                    'rate' => $taxRate->rate
                ]);
            }
        }

        // Secondary: Try to find by name
        if (!$taxRate && !empty($shortName)) {
            $taxRate = DB::table('tax_rates')
                ->where('business_id', $businessId)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($shortName))])
                ->first();
            
            if ($taxRate) {
                Log::debug('Tax rate found by name', [
                    'short_name' => $shortName,
                    'tax_rate_id' => $taxRate->id,
                    'rate' => $taxRate->rate
                ]);
            }
        }

        // Fallback 1: Get default tax rate (standard rate with rate > 0)
        if (!$taxRate) {
            $taxRate = DB::table('tax_rates')
                ->where('business_id', $businessId)
                ->where('is_activated', true)
                ->where('rate', '>', 0)
                ->orderBy('rate', 'desc')
                ->first();
            
            if ($taxRate) {
                Log::warning('Using default tax rate as fallback', [
                    'short_name' => $shortName,
                    'tax_rate_id' => $taxRate->id,
                    'rate' => $taxRate->rate
                ]);
            }
        }

        // Fallback 2: Use zero-rated tax if no other found
        if (!$taxRate) {
            $taxRate = DB::table('tax_rates')
                ->where('business_id', $businessId)
                ->where('is_activated', true)
                ->where('rate', 0)
                ->first();
            
            if ($taxRate) {
                Log::warning('Using zero-rated tax as final fallback', [
                    'short_name' => $shortName,
                    'tax_rate_id' => $taxRate->id
                ]);
            }
        }

        // Fallback 3: Get any tax rate
        if (!$taxRate) {
            $taxRate = DB::table('tax_rates')
                ->where('business_id', $businessId)
                ->where('is_activated', true)
                ->first();
            
            if ($taxRate) {
                Log::warning('Using any available tax rate as final fallback', [
                    'short_name' => $shortName,
                    'tax_rate_id' => $taxRate->id,
                    'rate' => $taxRate->rate
                ]);
            }
        }

        // Cache the result
        if ($taxRate) {
            $this->taxRateCache[$cacheKey] = $taxRate;
        }

        Log::debug('Tax rate lookup result', [
            'business_id' => $businessId,
            'short_name' => $shortName,
            'found' => !empty($taxRate),
            'rate' => $taxRate->rate ?? null,
            'tax_rate_id' => $taxRate->id ?? null
        ]);

        return $taxRate;
    }

    /**
     * Get sell price with dual fallbacks and tax consideration.
     */
    private function getSellPrice(array $item, $variation): float
    {
        // Primary: Use existing price
        if ($variation->exists && $variation->sell_price_inc_tax > 0) {
            return $variation->sell_price_inc_tax;
        }
        
        // Secondary: Extract from item
        $price = (float) ($item['price'] ?? $item['sellingPrice'] ?? $item['unitPrice'] ?? 0);
        if ($price > 0) {
            return $price;
        }
        
        // Fallback 1: Calculate from cost with margin
        $cost = (float) ($item['cost'] ?? 0);
        if ($cost > 0) {
            $margin = $item['profitMargin'] ?? 20;
            $calculatedPrice = $cost * (1 + ($margin / 100));
            Log::warning('Calculating price from cost as fallback', [
                'cost' => $cost,
                'calculated_price' => $calculatedPrice
            ]);
            return $calculatedPrice;
        }
        
        // Fallback 2: Use default 0
        Log::warning('No price available, using 0 as final fallback');
        return 0;
    }

    /**
     * Get cost price with dual fallbacks.
     */
    private function getCostPrice(array $item, $variation): float
    {
        // Primary: Use existing cost
        if ($variation->exists && $variation->default_purchase_price > 0) {
            return $variation->default_purchase_price;
        }
        
        // Secondary: Extract from item
        $cost = (float) ($item['cost'] ?? $item['purchasePrice'] ?? $item['buyingPrice'] ?? 0);
        if ($cost > 0) {
            return $cost;
        }
        
        // Fallback 1: Estimate from price
        $price = (float) ($item['price'] ?? $item['sellingPrice'] ?? 0);
        if ($price > 0) {
            $estimatedCost = $price * 0.7;
            Log::warning('Estimating cost from price as fallback', [
                'price' => $price,
                'estimated_cost' => $estimatedCost
            ]);
            return $estimatedCost;
        }
        
        // Fallback 2: Use default 0
        Log::warning('No cost available, using 0 as final fallback');
        return 0;
    }

    /**
     * Get stock with dual fallbacks.
     */
    private function getStockWithFallbacks(array $item): float
    {
        // Primary: Extract from item
        $stock = (float) ($item['stock'] ?? $item['quantity'] ?? $item['stockQuantity'] ?? 0);
        if ($stock >= 0) {
            return $stock;
        }
        
        // Fallback 1: Check variations
        if (isset($item['variations']) && is_array($item['variations'])) {
            foreach ($item['variations'] as $variation) {
                $varStock = (float) ($variation['stock'] ?? $variation['quantity'] ?? 0);
                if ($varStock >= 0) {
                    Log::warning('Using variation stock as fallback', [
                        'original_stock' => $stock,
                        'variation_stock' => $varStock
                    ]);
                    return $varStock;
                }
            }
        }
        
        // Fallback 2: Use default 0
        Log::warning('No stock available, using 0 as final fallback');
        return 0;
    }

    /**
     * Get location with dual fallbacks.
     */
    private function getLocationWithFallbacks(int $businessId, array $item): ?int
    {
        $siteId = $item['site_id'] ?? $item['siteId'] ?? $item['locationId'] ?? null;
        
        // Primary: Use site ID
        if ($siteId) {
            $locationId = $this->getLocationFromSite($businessId, $siteId);
            if ($locationId) {
                return $locationId;
            }
        }
        
        // Fallback 1: Check nested location
        if (isset($item['location']) && is_array($item['location'])) {
            $nestedSiteId = $item['location']['id'] ?? $item['location']['siteId'] ?? null;
            if ($nestedSiteId) {
                $locationId = $this->getLocationFromSite($businessId, $nestedSiteId);
                if ($locationId) {
                    Log::warning('Using nested location as fallback', [
                        'site_id' => $nestedSiteId,
                        'location_id' => $locationId
                    ]);
                    return $locationId;
                }
            }
        }
        
        // Fallback 2: Get any location for business
        $anyLocation = DB::table('business_locations')
            ->where('business_id', $businessId)
            ->first();
        
        if ($anyLocation) {
            Log::warning('Using any available location as final fallback', [
                'location_id' => $anyLocation->id,
                'location_name' => $anyLocation->name
            ]);
            return $anyLocation->id;
        }
        
        return null;
    }

    /**
     * Get product name with dual fallbacks.
     */
    private function getProductName(array $item, Product $product, string $eisId): string
    {
        if ($product->exists && !empty($product->name)) {
            return $product->name;
        }
        
        $name = $item['name'] ?? $item['productName'] ?? $item['description'] ?? null;
        if ($name && !empty(trim($name))) {
            return trim($name);
        }
        
        $sku = $item['sku'] ?? $item['productCode'] ?? null;
        if ($sku && !empty(trim($sku))) {
            Log::warning('Using SKU as name fallback', [
                'eis_id' => $eisId,
                'sku' => $sku
            ]);
            return 'Product ' . trim($sku);
        }
        
        Log::warning('Using generated name from EIS ID as final fallback', [
            'eis_id' => $eisId
        ]);
        return 'Product-' . substr($eisId, 0, 8);
    }

    /**
     * Get product description with dual fallbacks.
     */
    private function getProductDescription(array $item, Product $product): ?string
    {
        if ($product->exists && !empty($product->product_description)) {
            return $product->product_description;
        }
        
        $desc = $item['productDescription'] ?? $item['description'] ?? null;
        if ($desc && !empty(trim($desc))) {
            return trim($desc);
        }
        
        $longDesc = $item['longDescription'] ?? $item['detailedDescription'] ?? null;
        if ($longDesc && !empty(trim($longDesc))) {
            Log::warning('Using long description as fallback');
            return trim($longDesc);
        }
        
        $name = $item['name'] ?? $product->name ?? null;
        if ($name && !empty(trim($name))) {
            return 'Product: ' . trim($name);
        }
        
        return null;
    }

    /**
     * Get product taxRate with dual fallbacks.
     */
    private function getProductTaxRateId(string $item, $businessId): ?string
    {
        $taxRateId = TaxRate::where('business_id', $businessId)->where('tax_rate_id', $item)->first();
        Log::info('Tax rate fetch', [$taxRateId]);
        return $taxRateId->id ?? null;
    }

    /**
     * Get product SKU with dual fallbacks.
     */
    private function getProductSku(array $item, Product $product, string $eisId): string
    {
        if ($product->exists && !empty($product->sku)) {
            return $product->sku;
        }
        
        $sku = $item['sku'] ?? $item['productCode'] ?? $item['code'] ?? null;
        if ($sku && !empty(trim($sku))) {
            return trim($sku);
        }
        
        $name = $item['name'] ?? $item['productName'] ?? null;
        if ($name && !empty(trim($name))) {
            $generatedSku = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 10));
            if ($generatedSku) {
                Log::warning('Generating SKU from name as fallback', [
                    'name' => $name,
                    'generated_sku' => $generatedSku
                ]);
                return $generatedSku;
            }
        }
        
        Log::warning('Generating SKU from EIS ID as final fallback', [
            'eis_id' => $eisId
        ]);
        return 'EIS-' . strtoupper(substr($eisId, 0, 8));
    }

    /**
     * Get expiry period with dual fallbacks.
     */
    private function getExpiryPeriod(array $item, Product $product): ?string
    {
        if ($product->exists && !empty($product->expiry_period)) {
            return $product->expiry_period;
        }
        
        $expiry = $item['expiry_period'] ?? $item['productExpiryDate'] ?? $item['expiryDate'] ?? null;
        if ($expiry && !empty(trim($expiry))) {
            return trim($expiry);
        }
        
        $expiryDays = $item['expiryDays'] ?? $item['shelfLife'] ?? null;
        if ($expiryDays && is_numeric($expiryDays) && $expiryDays > 0) {
            Log::warning('Using expiry days as fallback', [
                'expiry_days' => $expiryDays
            ]);
            return $expiryDays . ' days';
        }
        
        return null;
    }

    /**
     * Save EIS product mapping safely.
     */
    private function saveProductMap(
        int $businessId,
        string $eisId,
        Product $product,
        ?string $sku
    ): void {

        $map = EisProductMap::withTrashed()
            ->where('business_id', $businessId)
            ->where('eis_product_id', $eisId)
            ->first();

        if ($map) {
            if ($map->trashed()) {
                $map->restore();
            }

            $map->update([
                'product_id' => $product->id,
                'sku' => $sku ?? $product->sku,
                'last_synced_at' => now(),
            ]);

            Log::debug('EIS product map updated', [
                'map_id' => $map->id,
                'product_id' => $product->id,
                'sku' => $sku
            ]);
        } else {
            EisProductMap::create([
                'business_id' => $businessId,
                'eis_product_id' => $eisId,
                'product_id' => $product->id,
                'sku' => $sku ?? $product->sku,
                'last_synced_at' => now(),
            ]);

            Log::debug('EIS product map created', [
                'business_id' => $businessId,
                'eis_product_id' => $eisId,
                'product_id' => $product->id,
                'sku' => $sku
            ]);
        }
    }

    /**
     * Calculate profit margin.
     */
    private function profit(
        float $price,
        float $cost,
        float $defaultPurchasePriceIncTax
    ): float {
        if ($cost <= 0) {
            return 0;
        }

        return (($price -  $defaultPurchasePriceIncTax) / $cost) * 100;
    }

    /**
     * Get POS location using EIS site ID.
     */
    private function getLocationFromSite(
        int $businessId,
        ?string $siteId
    ): ?int {
        if (empty($siteId)) {
            Log::warning('Missing EIS site id', [
                'business_id' => $businessId
            ]);
            return null;
        }

        // Check cache first
        $cacheKey = $businessId . ':' . $siteId;
        if (isset($this->locationCache[$cacheKey])) {
            return $this->locationCache[$cacheKey];
        }

        $locationId = DB::table('business_locations')
            ->where('business_id', $businessId)
            ->where('eis_site_id', $siteId)
            ->value('id');

        // Cache the result
        $this->locationCache[$cacheKey] = $locationId;

        Log::debug('EIS location lookup', [
            'business_id' => $businessId,
            'site_id' => $siteId,
            'location_id' => $locationId
        ]);

        return $locationId;
    }

    /**
     * Find product unit with dual fallbacks.
     */
    private function getUnitId(
        int $businessId,
        ?string $unitName
    ): ?int {
        if (empty($unitName)) {
            return null;
        }

        $unitName = strtolower(trim($unitName));
        
        // Check cache first
        $cacheKey = $businessId . ':' . $unitName;
        if (isset($this->unitCache[$cacheKey])) {
            return $this->unitCache[$cacheKey];
        }

        $unitId = DB::table('units')
            ->where('business_id', $businessId)
            ->where(function ($query) use ($unitName) {
                $query->whereRaw('LOWER(short_name) = ?', [$unitName])
                    ->orWhereRaw('LOWER(actual_name) = ?', [$unitName]);
            })
            ->value('id');

        // Cache the result
        $this->unitCache[$cacheKey] = $unitId;

        Log::debug('EIS unit lookup', [
            'business_id' => $businessId,
            'unit' => $unitName,
            'unit_id' => $unitId,
        ]);

        return $unitId;
    }

    /**
     * Validate required EIS product fields.
     */
    private function validateProduct(array $item): void
    {
        if (empty($item['name'])) {
            throw new \Exception('EIS product name is required');
        }

        if (empty($item['sku'])) {
            Log::warning('EIS product has no SKU', [
                'name' => $item['name']
            ]);
        }
    }

    /**
     * Generate fallback SKU.
     */
    private function generateSku(string $eisId): string
    {
        return 'EIS-' . strtoupper($eisId);
    }
}