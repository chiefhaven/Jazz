<?php

namespace Modules\EIS\Services\Products;

use App\Product;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisProductMap;

class ProductUpsertService
{
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
            | FIND PRODUCT BY SKU
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
            | CREATE NEW PRODUCT
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
            | PRODUCT DATA
            |--------------------------------------------------------------------------
            */
            $isNew = !$product->exists;

            $product->business_id = $businessId;

            if ($isNew) {
                $product->type = $item['type'] ?? 'single';
                $product->created_by = $this->getSystemUserId($businessId);
                $product->enable_stock = false;
                $product->expiry_period_type = null;
            }

            /*
            |--------------------------------------------------------------------------
            | EIS CONTROLLED FIELDS
            |--------------------------------------------------------------------------
            */
            $product->name = $item['name'] ?? $product->name ?? 'Unnamed Product';
            $product->sku = $item['sku'] ?? $product->sku ?? 'EIS-' . $eisId;
            $product->eis_product_id = $eisId;
            $product->eis_last_synced_at = now();

            /*
            |--------------------------------------------------------------------------
            | UNIT MAPPING
            |--------------------------------------------------------------------------
            */
            $unitId = $this->getUnitId(
                $businessId,
                $item['unit_of_measure'] ?? $item['unit'] ?? null
            );

            if ($unitId) {
                $product->unit_id = $unitId;
            }

            /*
            |--------------------------------------------------------------------------
            | EXPIRY
            |--------------------------------------------------------------------------
            */
            $product->expiry_period = $item['expiry_period'] ?? $product->expiry_period;

            $product->save();

            Log::info('Product saved', [
                'product_id' => $product->id,
                'sku' => $product->sku,
                'new' => $isNew,
            ]);

            /*
            |--------------------------------------------------------------------------
            | CREATE / UPDATE EIS MAP
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
     * Get system user ID for the business.
     *
     * @param int $businessId
     * @return int
     */
    private function getSystemUserId(int $businessId): int
    {
        // Try to get the first admin user for this business
        $userId = DB::table('user_business')
            ->where('business_id', $businessId)
            ->where('is_admin', 1)
            ->value('user_id');

        if (!$userId) {
            // Fallback to the first user
            $userId = DB::table('user_business')
                ->where('business_id', $businessId)
                ->value('user_id');
        }

        if (!$userId) {
            // Fallback to any user with business access
            $userId = DB::table('user_business')
                ->value('user_id');
        }

        // Final fallback to system user ID from config
        if (!$userId) {
            $userId = config('eis.system_user_id', 1);
        }

        Log::debug('System user ID resolved', [
            'business_id' => $businessId,
            'user_id' => $userId
        ]);

        return (int) $userId;
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
                'sku' => $sku,
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
                'sku' => $sku,
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
        | UPDATE PRICES
        |--------------------------------------------------------------------------
        */
        $sellPrice = (float) ($item['price'] ?? 0);
        $cost = (float) ($item['cost'] ?? 0);

        $variation->update([
            'default_sell_price' => $sellPrice,
            'default_purchase_price' => $cost,
            'sell_price_inc_tax' => $sellPrice,
            'sub_sku' => $item['sku'] ?? $product->sku,
            'profit_percent' => $this->profit($sellPrice, $cost),
        ]);

        /*
        |--------------------------------------------------------------------------
        | LOCATION FROM EIS SITE ID
        |--------------------------------------------------------------------------
        */
        $locationId = $this->getLocationFromSite(
            $businessId,
            $item['site_id'] ?? null
        );

        if (!$locationId) {
            // Try to get default location
            $locationId = $this->getDefaultLocation($businessId);
            
            if (!$locationId) {
                Log::error('No location found for product', [
                    'business_id' => $businessId,
                    'product_id' => $product->id,
                    'site_id' => $item['site_id'] ?? null
                ]);
                throw new \Exception(
                    'EIS location not mapped: ' . ($item['site_id'] ?? 'NULL')
                );
            }
            
            Log::warning('Using default location for product', [
                'business_id' => $businessId,
                'product_id' => $product->id,
                'location_id' => $locationId
            ]);
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
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | STOCK LOCATION
        |--------------------------------------------------------------------------
        */
        DB::table('variation_location_details')->updateOrInsert(
            [
                'variation_id' => $variation->id,
                'location_id' => $locationId,
            ],
            [
                'product_id' => $product->id,
                'product_variation_id' => $productVariationId,
                'qty_available' => $item['stock'] ?? 0,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        Log::info('Product inventory synced', [
            'product_id' => $product->id,
            'variation_id' => $variation->id,
            'location_id' => $locationId,
            'stock' => $item['stock'] ?? 0,
        ]);

        return $product;
    }

    /**
     * Calculate profit margin.
     */
    private function profit(
        float $price,
        float $cost
    ): float {
        if ($cost <= 0) {
            return 0;
        }

        return round((($price - $cost) / $cost) * 100, 2);
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

        $locationId = DB::table('business_locations')
            ->where('business_id', $businessId)
            ->where('eis_site_id', $siteId)
            ->value('id');

        Log::debug('EIS location lookup', [
            'business_id' => $businessId,
            'site_id' => $siteId,
            'location_id' => $locationId
        ]);

        return $locationId;
    }

    /**
     * Get default location for business.
     *
     * @param int $businessId
     * @return int|null
     */
    private function getDefaultLocation(int $businessId): ?int
    {
        return DB::table('business_locations')
            ->where('business_id', $businessId)
            ->where('is_default', 1)
            ->value('id');
    }

    /**
     * Find product unit.
     *
     * Matches:
     * - short_name
     * - actual_name
     *
     * Case insensitive.
     */
    private function getUnitId(
        int $businessId,
        ?string $unitName
    ): ?int {
        if (empty($unitName)) {
            return null;
        }

        $unitName = strtolower(trim($unitName));

        $unitId = DB::table('units')
            ->where('business_id', $businessId)
            ->where(function ($query) use ($unitName) {
                $query->whereRaw('LOWER(short_name) = ?', [$unitName])
                    ->orWhereRaw('LOWER(actual_name) = ?', [$unitName]);
            })
            ->value('id');

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