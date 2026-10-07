<?php

namespace App\Services\Sales;

use Illuminate\Database\Connection;

/** Legacy writers have no split-aware checkout contract: fence both bills, do not
 * accept an arbitrary client fingerprint as permission to overwrite saved lines.
 * Query-builder/bulk writers MUST call this before any graph/stock side effects.
 */
class SavedSaleWriteGuard
{
    private static $checkout = [];
    private static $stockAllocation = [];

    /** No callback is supplied by a caller. Only the verified reconciliation
     * implementation receives an exact-model, exact-attributes saving capability.
     * lock(), header, line, payment and fiscal model guards remain unchanged.
     */
    public static function reconcileStock($transaction)
    {
        $db = $transaction->getConnection();
        if ($db->transactionLevel() < 1 || self::$checkout || self::$stockAllocation) {
            throw new SavedSplitException('stock_reconciliation_invalid', 'Purchase reconciliation requires an enclosing transaction.');
        }
        return (new SavedSaleStockReconciler($db))->reconcile($transaction->getKey(), function ($model) use ($db) {
            if (!$model instanceof \App\TransactionSellLinesPurchaseLines || $model->getConnection() !== $db) {
                throw new SavedSplitException('stock_reconciliation_invalid', 'Invalid stock allocation capability.');
            }
            $key = spl_object_id($model);
            if (isset(self::$stockAllocation[$key])) {
                throw new SavedSplitException('stock_reconciliation_invalid', 'Reentrant allocation write.');
            }
            self::$stockAllocation[$key] = [$db, $db->transactionLevel(), $model->getAttributes(), $model->getRawOriginal()];
            $expected = $model->getAttributes();
            try {
                if ($model->save() !== true) {
                    throw new SavedSplitException('stock_reconciliation_invalid', 'An allocation observer cancelled the exact stock write.');
                }
                $persisted = (array) $db->table('transaction_sell_lines_purchase_lines')->where('id', $model->getKey())->first();
                foreach ($expected as $field => $value) {
                    if (in_array($field, ['created_at', 'updated_at'], true)) { continue; }
                    if (!array_key_exists($field, $persisted) || ($value === null ? $persisted[$field] !== null :
                        (string) $persisted[$field] !== (string) $value)) {
                        throw new SavedSplitException('stock_reconciliation_invalid', 'An allocation observer changed the exact planned stock write.');
                    }
                }
            } finally { unset(self::$stockAllocation[$key]); }
        });
    }

    public static function isStockAllocation($model)
    {
        $scope = self::$stockAllocation[spl_object_id($model)] ?? null;
        return $scope && $model instanceof \App\TransactionSellLinesPurchaseLines
            && $model->getConnection() === $scope[0] && $scope[0]->transactionLevel() === $scope[1]
            && $model->getAttributes() === $scope[2] && $model->getRawOriginal() === $scope[3];
    }

    /** Server-only scope, entered AFTER version/authorization/eligibility checks.
     * It never survives the callback, exception, transaction or request.
     */
    public static function checkout(Connection $db, $id, callable $work)
    {
        if ($db->transactionLevel() < 1) {
            throw new SavedSplitException('checkout_requires_transaction', 'Checkout requires a locked database transaction.');
        }
        $key = spl_object_id($db).':'.$id;
        if (isset(self::$checkout[$key])) {
            throw new SavedSplitException('checkout_reentrant', 'Nested saved checkout is forbidden.');
        }
        self::$checkout[$key] = $db->transactionLevel();
        try { return $work(); } finally { unset(self::$checkout[$key]); }
    }

    public static function isCheckout(Connection $db, $id)
    {
        return isset(self::$checkout[spl_object_id($db).':'.$id]) && $db->transactionLevel() >= self::$checkout[spl_object_id($db).':'.$id];
    }

    public static function lock(Connection $db, $id)
    {
        if (!$id || !$db->getSchemaBuilder()->hasTable('saved_sale_split_operations')) {
            return null; // Feature cannot split anything before its migration.
        }
        $header = $db->table('transactions')->where('id', $id)->lockForUpdate()->first();
        if (!$header || $header->type !== 'sell') {
            return $header;
        }
        // Never use a consistent-read snapshot here: a split may have committed
        // while this writer waited for the header lock.
        $split = $db->table('saved_sale_split_operations')
            ->where('business_id', $header->business_id)
            ->where(function ($q) use ($id) {
                $q->where('source_id', $id)->orWhere('result_id', $id);
            })->lockForUpdate()->first();
        if ($split && !self::isCheckout($db, $id)) {
            throw new SavedSplitException('split_write_fenced',
                'This saved split bill is read-only to legacy writers. Split-aware checkout is required.');
        }
        if (in_array($header->status, ['draft', 'final'], true) && $db->transactionLevel() < 1) {
            throw new SavedSplitException('draft_write_requires_transaction',
                'A saved draft writer must open a database transaction before locking the sale.');
        }

        return $header;
    }

    /** Fiscal tracking is not a legacy sale edit. Only a durably checked-out,
     * committed paid split may reach MyISAM/EIS, never during checkout itself.
     */
    public static function fiscal(Connection $db, $id)
    {
        return $db->transaction(function () use ($db, $id) {
            $h = $db->table('transactions')->where('id', $id)->lockForUpdate()->first();
            if (!$h || $h->type !== 'sell') { return $h; }
            if ($db->getSchemaBuilder()->hasTable('saved_sale_split_operations')) {
                $rows = $db->table('saved_sale_split_operations')->where('business_id', $h->business_id)
                    ->where(function ($q) use ($id) { $q->where('source_id', $id)->orWhere('result_id', $id); })
                    ->lockForUpdate()->get();
                if ($rows->count()) {
                    $completed = $rows->contains(function ($r) {
                        $allocation = json_decode($r->allocation_json, true);
                        return ($allocation['kind'] ?? null) === 'checkout' && !empty($r->response_json);
                    });
                    if (!$completed || $h->status !== 'final' || $h->payment_status !== 'paid' || $db->transactionLevel() > 1) {
                        throw new SavedSplitException('split_write_fenced', 'Only committed paid split checkouts may create fiscal tracking.');
                    }
                }
            }
            if ($h->status === 'draft') {
                throw new SavedSplitException('draft_fiscal_state_blocked', 'Draft sales must not create fiscal tracking.');
            }
            if ($h->payment_status !== 'paid') {
                throw new SavedSplitException('unpaid_fiscal_state_blocked', 'Unpaid/partial sales must not create fiscal tracking.');
            }
            return $h;
        });
    }

    /** Only copy the verified fiscal receipt projection, never stale Eloquent
     * attributes, lines, status, payments or arbitrary supplied fields.
     */
    public static function recordFiscalReceipt(Connection $db, $id)
    {
        self::fiscal($db, $id);
        return $db->transaction(function () use ($db, $id) {
            $h = $db->table('transactions')->where('id', $id)->lockForUpdate()->first();
            $receipt = $db->table('eis_sales')->where('transaction_id', $id)->where('business_id', $h->business_id)
                ->where('status', 'submitted')->first();
            if ($h->type !== 'sell' || $h->status !== 'final' || $h->payment_status !== 'paid' || !$receipt) {
                throw new SavedSplitException('fiscal_receipt_unverified', 'A committed paid sale and submitted tenant receipt are required.');
            }
            $db->table('transactions')->where('id', $id)->where('business_id', $h->business_id)->update([
                'is_fiscalized' => 1, 'fiscal_invoice_number' => $receipt->fiscal_invoice_number,
                'receipt_number' => $receipt->receipt_number]);
        });
    }

    public static function model($model, $headerModel = false)
    {
        $ids = $headerModel
            ? [$model->exists ? $model->getKey() : null, $model->return_parent_id, $model->recur_parent_id, $model->transfer_parent_id]
            : [$model->getRawOriginal('transaction_id'), $model->transaction_id];
        $ids = array_unique(array_filter($ids));
        sort($ids, SORT_NUMERIC);
        foreach ($ids as $id) {
            self::lock($model->getConnection(), $id);
        }
    }
}