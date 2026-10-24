<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSale;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Terminal\TerminalContext;
use Modules\EIS\Services\Utilities\EisUtilitiesClient;

class SaleAdjustmentService
{
    public function __construct(private SalesApiClient $client, private ReturnAdjustmentPayload $transformer) {}

    public function beforeReturn(Transaction $sale, int $actorId): ?EisSale
    {
        if (!DB::getSchemaBuilder()->hasTable('eis_sales')) { return null; }
        $fiscal = EisSale::where('business_id', $sale->business_id)->where('transaction_id', $sale->id)->first();
        if (!$fiscal) { return null; }
        if ($fiscal->status !== 'submitted') {
            throw new \InvalidArgumentException('Finish fiscal submission before returning an EIS sale.');
        }
        if (DB::connection()->transactionLevel() < 1) {
            throw new \InvalidArgumentException('EIS returns require an atomic local ledger transaction.');
        }
        $this->storage();
        DB::table('business')->where('id', $sale->business_id)->lockForUpdate()->first();
        DB::table('transactions')->where('id', $sale->id)->lockForUpdate()->first();
        $setting = TerminalContext::settings((int) $sale->business_id, null, (int) $fiscal->eis_setting_id, (int) $sale->location_id);
        TerminalContext::assertActive($setting);
        $actor = \App\User::where('business_id', $sale->business_id)->findOrFail($actorId);
        $locations = $actor->permitted_locations((int) $sale->business_id);
        if (($locations !== 'all' && !in_array((int) $sale->location_id, array_map('intval', (array) $locations), true))
            || (!$actor->can('access_sell_return') && !($actor->can('access_own_sell_return') && (int) $sale->created_by === $actorId))) {
            throw new \InvalidArgumentException('You do not have permission to return this fiscal sale.');
        }
        if (DB::table('eis_sale_adjustments')->where('business_id', $sale->business_id)->where('sale_id', $sale->id)
            ->where('status', '!=', 'accepted')->lockForUpdate()->exists()) {
            throw new \InvalidArgumentException('Resolve the existing EIS credit/debit note before editing this return. Never resend an uncertain request.');
        }
        if (DB::table('eis_utility_requests')->where('business_id', $sale->business_id)->where('operation', 'cancel-receipt')
            ->where('status', '!=', 'rejected')->get()->contains(function ($request) use ($fiscal) {
                $number = json_decode($request->payload, true, 512, JSON_THROW_ON_ERROR)['receiptNumber'] ?? null;
                return in_array($number, array_filter([$fiscal->receipt_number, $fiscal->fiscal_invoice_number, $fiscal->eis_invoice_number]), true);
            })) {
            throw new \InvalidArgumentException('This invoice has a void request. Verify its outcome before processing a return.');
        }
        return $fiscal;
    }

    public function capture(Transaction $sale, Transaction $return, EisSale $fiscal, int $actorId): void
    {
        if (DB::connection()->transactionLevel() < 1) {
            throw new \InvalidArgumentException('EIS return capture must commit with the local return ledger.');
        }
        $setting = TerminalContext::settings((int) $sale->business_id, null, (int) $fiscal->eis_setting_id, (int) $sale->location_id);
        $original = $fiscal->request_payload;
        if (is_string($original)) { $original = json_decode($original, true, 512, JSON_THROW_ON_ERROR); }
        if (!is_array($original)) { throw new \InvalidArgumentException('Original fiscal payload is unavailable; the return cannot be safely mapped.'); }
        if (($original['invoiceHeader']['siteId'] ?? null) !== TerminalContext::site($setting)
            || ($original['invoiceHeader']['sellerTIN'] ?? null) !== $setting->tpin) {
            throw new \InvalidArgumentException('Original fiscal invoice does not match its terminal ownership.');
        }
        $lines = $this->lines((int) $sale->id);
        $last = DB::table('eis_sale_adjustments')->where('business_id', $sale->business_id)
            ->where('sale_id', $sale->id)->orderByDesc('id')->lockForUpdate()->first();
        if (!$last && collect($lines)->contains(fn ($line) => (float) $line['quantity_returned'] > 0)
            && $return->wasRecentlyCreated === false) {
            throw new \InvalidArgumentException('Historical returns need EIS verification before their first linked adjustment.');
        }
        $number = $last ? $last->invoice_number : ($fiscal->eis_invoice_number ?: $fiscal->fiscal_invoice_number);
        if (!$number) { throw new \InvalidArgumentException('Original fiscal invoice number is missing.'); }
        $payload = $this->transformer->build($original, $lines, (float) $return->final_total, $number);
        $previous = $last ? json_decode($last->payload, true, 512, JSON_THROW_ON_ERROR) : $original;
        $before = (float) $previous['invoiceSummary']['invoiceTotal'];
        $after = (float) $payload['invoiceSummary']['invoiceTotal'];
        $vatChange = (float) $payload['invoiceSummary']['totalVAT'] - (float) $previous['invoiceSummary']['totalVAT'];
        if (($after < $before && $vatChange > 0.005) || ($after > $before && $vatChange < -0.005)) {
            throw new \InvalidArgumentException('Mixed credit/debit tax changes require separate reviewed adjustments.');
        }
        if (abs($before - $after) < 0.005) {
            if ($last && $this->snapshot($return, $lines) === $last->local_snapshot) { return; }
            throw new \InvalidArgumentException('A fiscal return must change the invoice value; tax-neutral product exchanges need manual review.');
        }
        // Refunds already paid cannot exceed the newly reduced credit balance.
        $paid = (float) DB::table('transaction_payments')->where('business_id', $sale->business_id)
            ->where('transaction_id', $return->id)->sum('amount');
        if ($paid > (float) $return->final_total + 0.005) {
            throw new \InvalidArgumentException('Reverse the excess refund payment before reducing this return.');
        }
        DB::table('eis_sale_adjustments')->insert([
            'business_id' => $sale->business_id, 'location_id' => $sale->location_id, 'eis_setting_id' => $setting->id,
            'sale_id' => $sale->id, 'return_id' => $return->id, 'created_by' => $actorId,
            'endpoint' => EisUtilitiesClient::baseUrl(), 'tin' => $setting->tpin, 'site_id' => TerminalContext::site($setting),
            'device_id' => $setting->device_id, 'status' => 'prepared', 'note_type' => $after < $before ? 'Credit' : 'Debit',
            'payload' => json_encode($payload, JSON_THROW_ON_ERROR), 'local_snapshot' => $this->snapshot($return, $lines),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function submit(EisSetting $setting, int $id, int $actorId): object
    {
        $this->storage();
        if (DB::connection()->transactionLevel() !== 0) {
            throw new \InvalidArgumentException('Send EIS adjustments outside a local ledger transaction.');
        }
        [$row, $claimed, $setting] = DB::transaction(function () use ($setting, $id, $actorId) {
            DB::table('business')->where('id', $setting->business_id)->lockForUpdate()->first();
            $fresh = EisSetting::where('business_id', $setting->business_id)->lockForUpdate()->findOrFail($setting->id);
            TerminalContext::assertActive($fresh);
            $row = DB::table('eis_sale_adjustments')->where('business_id', $fresh->business_id)
                ->where('eis_setting_id', $fresh->id)->where('id', $id)->lockForUpdate()->first();
            if (!$row) { throw new \InvalidArgumentException('Select an adjustment belonging to this terminal.'); }
            if ($row->status !== 'prepared') { return [$row, false, $fresh]; }
            if ($row->endpoint !== EisUtilitiesClient::baseUrl() || $row->tin !== $fresh->tpin
                || $row->site_id !== TerminalContext::site($fresh) || $row->device_id !== $fresh->device_id
                || (int) $row->location_id !== (int) $fresh->location_id) {
                throw new \InvalidArgumentException('Saved adjustment identity changed. Verify with MRA before proceeding.');
            }
            $return = Transaction::where('business_id', $row->business_id)->where('type', 'sell_return')
                ->where('return_parent_id', $row->sale_id)->lockForUpdate()->findOrFail($row->return_id);
            DB::table('transactions')->where('id', $row->sale_id)->lockForUpdate()->first();
            if ($row->local_snapshot !== $this->snapshot($return, $this->lines((int) $row->sale_id))) {
                throw new \InvalidArgumentException('Local return changed after preparation. Do not submit a stale fiscal adjustment.');
            }
            DB::table('eis_sale_adjustments')->where('id', $id)->update([
                'status' => 'sending', 'submitted_by' => $actorId, 'submitted_at' => now(), 'updated_at' => now(),
            ]);
            return [$row, true, $fresh];
        });
        if (!$claimed) { return $row; }
        $payload = json_decode($row->payload, true, 512, JSON_THROW_ON_ERROR);
        try {
            $result = $this->client->send($setting, 'process-credit-debit-note', $payload);
        } catch (\Throwable $exception) {
            Log::error('EIS adjustment outcome unknown', ['adjustment_id' => $id, 'exception' => get_class($exception)]);
            $result = ['outcome' => 'unknown', 'response' => []];
        }
        $status = $result['outcome']; $data = $result['response']['data'] ?? [];
        if ($status === 'accepted' && (!is_string($data['invoiceNumber'] ?? null) || trim($data['invoiceNumber']) === ''
            || ($data['originalInvoiceNumber'] ?? null) !== $payload['invoiceHeader']['invoiceNumber']
            || strcasecmp((string) ($data['noteType'] ?? ''), $row->note_type.' Note') !== 0
                && strcasecmp((string) ($data['noteType'] ?? ''), $row->note_type) !== 0
            || !is_numeric($data['invoiceTotal'] ?? null) || !is_numeric($data['totalVat'] ?? null)
            || !is_finite((float) $data['invoiceTotal']) || !is_finite((float) $data['totalVat'])
            || abs((float) $data['invoiceTotal'] - $payload['invoiceSummary']['invoiceTotal']) > 0.01
            || abs((float) $data['totalVat'] - $payload['invoiceSummary']['totalVAT']) > 0.01)) {
            $status = 'needs_review';
        }
        $message = $status === 'accepted' ? 'EIS note accepted. The linked POS return already accounts for stock and customer credit; nothing was applied twice.'
            : 'EIS adjustment '.$status.'. Local return remains recorded. Verify the result before further edits; this request cannot be resent.';
        DB::table('eis_sale_adjustments')->where('id', $id)->update([
            'status' => $status, 'invoice_number' => $status === 'accepted' ? $data['invoiceNumber'] : null,
            'response' => json_encode($result['response'], JSON_THROW_ON_ERROR), 'result_message' => $message, 'updated_at' => now(),
        ]);
        return DB::table('eis_sale_adjustments')->find($id);
    }

    private function lines(int $saleId): array
    {
        return DB::table('transaction_sell_lines as l')->join('products as p', 'p.id', '=', 'l.product_id')
            ->where('l.transaction_id', $saleId)->orderBy('l.id')->lockForUpdate()
            ->get(['l.id', 'l.quantity', 'l.quantity_returned', 'l.unit_price_inc_tax', 'l.item_tax',
                'l.parent_sell_line_id', 'p.sku'])->map(fn ($line) => (array) $line)->all();
    }

    private function snapshot(Transaction $return, array $lines): string
    {
        return json_encode(['return_id' => $return->id, 'business_id' => $return->business_id,
            'location_id' => $return->location_id, 'sale_id' => $return->return_parent_id,
            'total' => number_format((float) $return->final_total, 4, '.', ''), 'lines' => $lines], JSON_THROW_ON_ERROR);
    }

    private function storage(): void
    {
        foreach (['business', 'transactions', 'transaction_sell_lines', 'purchase_lines', 'transaction_sell_lines_purchase_lines',
            'variation_location_details', 'eis_sale_adjustments'] as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                throw new \InvalidArgumentException('Run EIS Sales migrations before processing fiscal returns.');
            }
            if (DB::connection()->getDriverName() === 'mysql') {
                $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
                if (!$engine || strcasecmp($engine->engine, 'InnoDB') !== 0) {
                    throw new \InvalidArgumentException('Fiscal returns require InnoDB storage: '.$table);
                }
            }
        }
    }
}
