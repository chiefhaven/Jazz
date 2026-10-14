<?php
namespace Modules\EIS\Services\Products;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Log;
/** One-time supply, never an EIS-to-local stock reconciliation. */
final class OpeningStockInitializer
{
    public function __construct(private Connection $db) {}
    /** DECIMAL(22,4), without coercion, rounding, scientific notation or floats. */
    public static function decimal($value): ?string
    {
        if (!is_string($value) && !is_int($value)) {
            return null;
        }
        $value = (string) $value;
        if (!preg_match('/^\d+(?:\.\d{1,4})?$/D', $value)) {
            return null;
        }
        $integer = ltrim(explode('.', $value)[0], '0');
        if (strlen($integer) > 18) {
            return null;
        }
        return bcadd($value, '0', 4);
    }
    /** Preserve absence/invalidity before the catalogue transform's float fallbacks. */
    public static function stockSource(array $item): array
    {
        if (isset($item['variations']) && is_array($item['variations']) && count($item['variations']) > 1) {
            return ['quantity' => null, 'reason' => 'ambiguous_source_variations'];
        }
        $quantity = null;
        $fields = [];
        foreach (['quantity', 'stock', 'stockQuantity'] as $field) {
            if (!array_key_exists($field, $item)) {
                continue;
            }
            $fields[] = $field;
            $value = $item[$field];
            if (is_float($value)) {
                // Validate only the decoded payload, not the original JSON token's precision.
                // A noncanonical encoder configuration could silently round; fail closed.
                if (!is_finite($value) || $value < 0 || $value > 1e9
                    || ini_get('serialize_precision') !== '-1') {
                    return ['quantity' => null, 'reason' => 'invalid_or_conflicting_stock'];
                }
                $value = json_encode($value, JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);
            }
            $next = self::decimal($value);
            if ($next === null || ($quantity !== null && $quantity !== $next)) {
                return ['quantity' => null, 'reason' => 'invalid_or_conflicting_stock'];
            }
            $quantity = $next;
        }
        return ['quantity' => $quantity, 'reason' => $fields ? null : 'missing_stock'];
    }
    /** Reject conflicting aliases and nonscalar sites; never invent a local site. */
    public static function siteId(array $item): ?string
    {
        $site = null;
        $values = [];
        foreach (['siteId', 'site_id', 'locationId'] as $field) {
            if (array_key_exists($field, $item)) {
                $values[] = $item[$field];
            }
        }
        if (isset($item['location']) && is_array($item['location'])) {
            foreach (['id', 'siteId'] as $field) {
                if (array_key_exists($field, $item['location'])) {
                    $values[] = $item['location'][$field];
                }
            }
        }
        foreach ($values as $value) {
            if ((!is_string($value) && !is_int($value)) || trim((string) $value) === '') {
                return null;
            }
            $value = trim((string) $value);
            if ($site !== null && $site !== $value) {
                return null;
            }
            $site = $value;
        }
        return $site;
    }
    /** Missing item sites may inherit only the validated site of this exact request. */
    public static function scopedSiteId(array $item, ?string $requestSite): ?string
    {
        $explicit = false;
        foreach (['siteId', 'site_id', 'locationId'] as $field) {
            $explicit = $explicit || array_key_exists($field, $item);
        }
        if (array_key_exists('location', $item)) {
            if (!is_array($item['location'])) {
                return null;
            }
            $explicit = $explicit || array_key_exists('id', $item['location'])
                || array_key_exists('siteId', $item['location']);
        }
        if (!$explicit) {
            return $requestSite;
        }
        $site = self::siteId($item);
        return $site !== null && ($requestSite === null || $site === $requestSite) ? $site : null;
    }
    /** Caller owns the catalogue transaction. Business and product are read without explicit row locks. */
    public function initialize(int $businessId, int $productId, int $variationId,
        int $locationId, string $eisId, string $siteId, array $source): string
    {
        if ($this->db->transactionLevel() < 1) {
            throw new \LogicException('EIS opening stock requires an enclosing transaction.');
        }
        $context = compact('businessId', 'productId', 'variationId', 'locationId', 'eisId', 'siteId');
        $block = function (string $reason) use ($context): string {
            Log::warning('EIS opening stock not initialized: '.$reason, $context + [
                'action' => 'Review identity/site and reconcile historical stock and allocations manually; do not repull to repair stock.',
            ]);
            return $reason;
        };
        // MyISAM markers cannot commit/rollback with the supply graph. Do not pretend otherwise.
        if ($this->db->getDriverName() === 'mysql') {
            foreach (['business', 'products', 'product_variations', 'variations', 'business_locations',
                'eis_product_maps', 'transactions', 'purchase_lines', 'variation_location_details',
                'transaction_sell_lines', 'stock_adjustment_lines', 'transaction_sell_lines_purchase_lines'] as $table) {
                $row = $this->db->selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
                    [$this->db->getTablePrefix().$table], false);
                if (!$row || strtoupper((string) $row->engine) !== 'INNODB') {
                    return $block('nontransactional_storage:'.$table);
                }
            }
        } elseif ($this->db->getDriverName() !== 'sqlite') {
            return $block('unsupported_storage');
        }
        if (!$this->db->table('business')->where('id', $businessId)->first()) {
            return $block('missing_business');
        }
        $product = $this->db->table('products')->where('id', $productId)->first();
        $map = $this->db->table('eis_product_maps')->where('business_id', $businessId)
            ->where('eis_product_id', $eisId)->lockForUpdate()->first();
        $variation = $this->db->table('variations')->where('id', $variationId)->lockForUpdate()->first();
        $location = $this->db->table('business_locations')->where('id', $locationId)->lockForUpdate()->first();
        if ($businessId <= 0 || trim($eisId) === ''
            || !$product || (int) $product->business_id !== $businessId || $product->deleted_at !== null
            || !$map || (int) $map->product_id !== $productId || $map->deleted_at !== null
            || !$variation || (int) $variation->product_id !== $productId
            || $variation->deleted_at !== null
            || !$location || (int) $location->business_id !== $businessId || (string) $location->eis_site_id !== $siteId
            || $siteId === '' || $this->db->table('business_locations')->where('business_id', $businessId)->where('eis_site_id', $siteId)->count() !== 1
            || $this->db->table('eis_product_maps')->where('product_id', $productId)->count() !== 1
            || (string) $product->eis_product_id !== $eisId) {
            return $block('identity_conflict');
        }
        if (!$product->enable_stock) {
            return 'nonstock';
        }
        if ($product->type !== 'single' || $this->db->table('variations')->where('product_id', $productId)->count() !== 1
            || $this->db->table('product_variations')->where('product_id', $productId)->count() !== 1
            || !$this->db->table('product_variations')->where('id', $variation->product_variation_id)->where('product_id', $productId)->exists()) {
            return $block('ambiguous_variation');
        }
        $meta = $map->meta === null ? [] : json_decode($map->meta, true);
        if (!is_array($meta) || ($map->meta !== null && !str_starts_with(ltrim($map->meta), '{'))) {
            return $block('invalid_map_meta');
        }
        if (array_key_exists('eis_opening_stock_v1', $meta)) {
            $marker = $meta['eis_opening_stock_v1'];
            foreach ($context as $key => $value) {
                if (!is_array($marker) || !array_key_exists($key, $marker)
                    || (!is_string($marker[$key]) && !is_int($marker[$key]))
                    || (string) $marker[$key] !== (string) $value) {
                    return $block('opening_marker_identity_conflict');
                }
            }
            if (!isset($marker['transaction_id'], $marker['purchase_line_id'])
                || !is_int($marker['transaction_id']) || $marker['transaction_id'] <= 0
                || !is_int($marker['purchase_line_id']) || $marker['purchase_line_id'] <= 0
                || self::decimal($marker['quantity'] ?? null) === null) {
                return $block('invalid_opening_marker');
            }
            $header = $this->db->table('transactions')->where('id', $marker['transaction_id'])
                ->where('business_id', $businessId)->where('location_id', $locationId)
                ->where('opening_stock_product_id', $productId)->where('type', 'opening_stock')
                ->where('status', 'received')->lockForUpdate()->first();
            $line = $this->db->table('purchase_lines')->where('id', $marker['purchase_line_id'])
                ->where('transaction_id', $marker['transaction_id'])->where('product_id', $productId)
                ->where('variation_id', $variationId)->lockForUpdate()->first();
            if (!$header || !$line || self::decimal($line->quantity) !== self::decimal($marker['quantity'])) {
                return $block('opening_marker_ledger_missing_or_changed');
            }
            // A deleted/edited opening ledger must be reviewed, never recreated by another pull.
            return 'already_initialized';
        }
        if (isset($meta['eis_opening_stock_excluded'])) {
            return $block('existing_catalogue_product_not_import_owned');
        }
        $quantity = self::decimal($source['quantity'] ?? null);
        if ($quantity === null) {
            return $block($source['reason'] ?? 'invalid_stock');
        }
        // Fail closed even for orphaned lines and overselling sentinel allocations.
        // Looking across locations also prevents silently moving a historical site allocation.
        foreach (['purchase_lines', 'transaction_sell_lines', 'stock_adjustment_lines'] as $table) {
            if ($this->db->table($table)->where(function ($q) use ($productId, $variationId) {
                $q->where('product_id', $productId)->orWhere('variation_id', $variationId);
            })->lockForUpdate()->get()->isNotEmpty()) {
                return $block('historical_movement:'.$table);
            }
        }
        if ($this->db->table('transactions')->where('opening_stock_product_id', $productId)->lockForUpdate()->get()->isNotEmpty()) {
            return $block('existing_opening_stock_header');
        }
        $rows = $this->db->table('variation_location_details')->where(function ($q) use ($productId, $variationId) {
            $q->where('product_id', $productId)->orWhere('variation_id', $variationId);
        })->lockForUpdate()->get();
        $stockRow = null;
        foreach ($rows as $row) {
            if ((int) $row->product_id !== $productId || (int) $row->variation_id !== $variationId
                || (int) $row->product_variation_id !== (int) $variation->product_variation_id) {
                return $block('stock_row_identity_conflict');
            }
            $available = self::decimal($row->qty_available);
            if ($available === null || bccomp($available, '0', 4) !== 0) {
                return $block('legacy_nonzero_or_invalid_availability');
            }
            if ((int) $row->location_id !== $locationId || $stockRow !== null) {
                return $block('ambiguous_stock_location');
            }
            $stockRow = $row;
        }
        // Read DECIMAL directly; no model casts/float arithmetic in the new ledger logic.
        $cost = self::decimal($variation->default_purchase_price);
        $costInc = self::decimal($variation->dpp_inc_tax);
        if ($cost === null || $costInc === null || bccomp($costInc, $cost, 4) < 0) {
            return $block('invalid_opening_cost');
        }
        $totalEx = bcmul($quantity, $cost, 4);
        $totalInc = bcmul($quantity, $costInc, 4);
        $taxTotal = bcsub($totalInc, $totalEx, 4);
        $itemTax = bcsub($costInc, $cost, 4);
        foreach ([$totalEx, $totalInc, $taxTotal, $itemTax] as $amount) {
            if (self::decimal($amount) === null) {
                return $block('opening_total_overflow');
            }
        }
        $time = date('Y-m-d H:i:s');
        $transactionId = $this->db->table('transactions')->insertGetId([
            'business_id' => $businessId, 'location_id' => $locationId, 'type' => 'opening_stock',
            'opening_stock_product_id' => $productId, 'status' => 'received', 'payment_status' => 'paid',
            'created_by' => $product->created_by, 'transaction_date' => $time,
            'total_before_tax' => $totalEx, 'final_total' => $totalInc, 'tax_amount' => $taxTotal,
            'additional_notes' => 'One-time EIS opening stock: '.$eisId,
            'created_at' => $time, 'updated_at' => $time,
        ]);
        $lineId = $this->db->table('purchase_lines')->insertGetId([
            'transaction_id' => $transactionId, 'product_id' => $productId, 'variation_id' => $variationId,
            'quantity' => $quantity, 'pp_without_discount' => $cost, 'purchase_price' => $cost,
            'purchase_price_inc_tax' => $costInc, 'item_tax' => $itemTax, 'tax_id' => $product->tax,
            'quantity_sold' => '0.0000', 'quantity_adjusted' => '0.0000', 'quantity_returned' => '0.0000',
            'mfg_quantity_used' => '0.0000', 'created_at' => $time, 'updated_at' => $time,
        ]);
        if ($stockRow) {
            $this->db->table('variation_location_details')->where('id', $stockRow->id)
                ->update(['qty_available' => $quantity, 'updated_at' => $time]);
        } else {
            $this->db->table('variation_location_details')->insert([
                'product_id' => $productId, 'product_variation_id' => $variation->product_variation_id,
                'variation_id' => $variationId, 'location_id' => $locationId, 'qty_available' => $quantity,
                'created_at' => $time, 'updated_at' => $time,
            ]);
        }
        $meta['eis_opening_stock_v1'] = $context + ['quantity' => $quantity,
            'transaction_id' => $transactionId, 'purchase_line_id' => $lineId, 'initialized_at' => $time];
        $this->db->table('eis_product_maps')->where('id', $map->id)
            ->update(['meta' => json_encode($meta, JSON_THROW_ON_ERROR), 'updated_at' => $time]);
        return 'initialized';
    }
}