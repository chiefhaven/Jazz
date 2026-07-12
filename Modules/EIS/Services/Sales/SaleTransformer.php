<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Models\EisTaxRate;

class SaleTransformer
{
    public function __construct(
        protected EisTaxRate $taxRateModel
    ) {}

    /**
     * Transform transaction for EIS submission.
     *
     * @param Transaction $transaction
     * @param EisSetting $settings
     * @param string $eisInvoiceNumber
     * @return array
     */
    public function transform(Transaction $transaction, EisSetting $settings, string $eisInvoiceNumber): array
    {
        $transaction->loadMissing([
            'business',
            'location',
            'contact',
            'sell_lines.product',
            'sell_lines.tax',
        ]);

        $invoiceItems = [];
        $taxBreakdown = [];
        $totalVat = 0;
        $subtotal = 0;

        foreach ($transaction->sell_lines as $index => $line) {
            // Get tax rate ID from EIS tax rates
            $taxRateId = $this->getTaxRateId($settings->business_id, $line);

            $unitPrice = (float) $line->unit_price_inc_tax;
            $quantity = (float) $line->quantity;
            $vat = (float) $line->item_tax;
            $discount = (float) ($line->line_discount_amount ?? 0);

            $lineTotal = $unitPrice * $quantity;
            $subtotal += $lineTotal;

            // Get product details
            $productCode = $line->product->sku ?? $line->product->id ?? 'N/A';
            $productName = $line->product->name ?? $line->product_name ?? 'Unknown Product';

            $invoiceItems[] = [
                'id' => $index + 1,
                'productCode' => (string) $productCode,
                'description' => (string) $productName,
                'unitPrice' => $unitPrice,
                'quantity' => $quantity,
                'discount' => $discount,
                'total' => $lineTotal,
                'totalVAT' => $vat,
                'taxRateId' => (string) $taxRateId,
                'isProduct' => true,
            ];

            // Build tax breakdown
            if (!isset($taxBreakdown[$taxRateId])) {
                $taxBreakdown[$taxRateId] = [
                    'rateId' => (string) $taxRateId,
                    'taxableAmount' => 0,
                    'taxAmount' => 0,
                ];
            }

            $taxBreakdown[$taxRateId]['taxableAmount'] += $lineTotal;
            $taxBreakdown[$taxRateId]['taxAmount'] += $vat;
            $totalVat += $vat;
        }

        // Get site ID from settings
        $siteId = $settings->branch_id ?? $settings->site_id ?? $settings->device_id ?? '00';

        return [
            'invoiceHeader' => [
                'invoiceNumber' => (string) $eisInvoiceNumber,
                'invoiceDateTime' => $transaction->transaction_date
                    ? \Carbon\Carbon::parse($transaction->transaction_date)->toIso8601String()
                    : now()->toIso8601String(),
                'sellerTIN' => (string) $settings->tpin,
                'buyerTIN' => (string) optional($transaction->contact)->tax_number,
                'buyerName' => (string) optional($transaction->contact)->name,
                'buyerAuthorizationCode' => '',
                'siteId' => (string) $siteId,
                'globalConfigVersion' => (int) ($settings->global_version ?? 0),
                'taxpayerConfigVersion' => (int) ($settings->taxpayer_version ?? 0),
                'terminalConfigVersion' => (int) ($settings->terminal_version ?? 0),
                'isExport' => false,
                'isReliefSupply' => false,
                'vat5CertificateDetails' => null,
                'paymentMethod' => strtoupper($transaction->payment_method ?? 'CASH'),
            ],
            'invoiceLineItems' => $invoiceItems,
            'invoiceSummary' => [
                'taxBreakDown' => array_values($taxBreakdown),
                'levyBreakDown' => [],
                'totalVAT' => (float) $totalVat,
                'offlineSignature' => '',
                'invoiceTotal' => (float) $transaction->final_total,
                'amountTendered' => (float) $transaction->final_total,
            ],
        ];
    }

    /**
     * Get EIS tax rate ID from transaction line.
     *
     * @param int $businessId
     * @param mixed $line
     * @return string
     */
    protected function getTaxRateId(int $businessId, $line): string
    {
        try {
            // If line has tax_id, try to map it
            if (!empty($line->tax_id)) {
                $taxRate = $this->taxRateModel
                    ->where('business_id', $businessId)
                    ->where('tax_rate_id', $line->tax_id)
                    ->first();

                if ($taxRate) {
                    return $taxRate->tax_rate_id;
                }
            }

            // Try to find by rate
            $rate = (float) $line->item_tax / max(1, $line->unit_price_inc_tax * $line->quantity) * 100;
            
            $taxRate = $this->taxRateModel
                ->where('business_id', $businessId)
                ->where('is_activated', true)
                ->where('rate', '>=', $rate - 0.01)
                ->where('rate', '<=', $rate + 0.01)
                ->first();

            if ($taxRate) {
                return $taxRate->tax_rate_id;
            }

            // Default to standard rate or 'A'
            $defaultRate = $this->taxRateModel
                ->where('business_id', $businessId)
                ->where('is_activated', true)
                ->where('rate', '>', 0)
                ->orderBy('rate', 'desc')
                ->first();

            return $defaultRate->tax_rate_id ?? 'A';

        } catch (\Exception $e) {
            Log::warning('Failed to resolve tax rate ID', [
                'business_id' => $businessId,
                'tax_id' => $line->tax_id ?? null,
                'error' => $e->getMessage()
            ]);
            return 'A'; // Default tax rate
        }
    }

    /**
     * Transform items for EIS format.
     *
     * @param array $items
     * @return array
     */
    public function transformItems(array $items): array
    {
        $transformed = [];
        
        foreach ($items as $index => $item) {
            $transformed[] = [
                'id' => $index + 1,
                'productCode' => (string) ($item['code'] ?? $item['sku'] ?? 'N/A'),
                'description' => (string) ($item['name'] ?? $item['description'] ?? 'Unknown Product'),
                'unitPrice' => (float) ($item['unit_price'] ?? 0),
                'quantity' => (float) ($item['quantity'] ?? 1),
                'discount' => (float) ($item['discount'] ?? 0),
                'total' => (float) ($item['total'] ?? 0),
                'totalVAT' => (float) ($item['tax_amount'] ?? 0),
                'taxRateId' => (string) ($item['tax_rate_id'] ?? 'A'),
                'isProduct' => true,
            ];
        }
        
        return $transformed;
    }

    /**
     * Transform customer data.
     *
     * @param Transaction $transaction
     * @return array
     */
    public function transformCustomer(Transaction $transaction): array
    {
        return [
            'name' => $transaction->customer_name ?? $transaction->contact->name ?? 'Walk-in Customer',
            'phone' => $transaction->customer_phone ?? $transaction->contact->mobile ?? null,
            'email' => $transaction->customer_email ?? $transaction->contact->email ?? null,
            'tpin' => $transaction->customer_tpin ?? $transaction->contact->tax_number ?? null,
        ];
    }

    /**
     * Transform totals.
     *
     * @param Transaction $transaction
     * @return array
     */
    public function transformTotals(Transaction $transaction): array
    {
        return [
            'subtotal' => (float) ($transaction->subtotal ?? 0),
            'tax' => (float) ($transaction->tax_amount ?? 0),
            'discount' => (float) ($transaction->discount_amount ?? 0),
            'total' => (float) ($transaction->final_total ?? 0),
        ];
    }

    /**
     * Transform payment.
     *
     * @param Transaction $transaction
     * @return array
     */
    public function transformPayment(Transaction $transaction): array
    {
        return [
            'method' => $transaction->payment_method ?? 'Cash',
            'amount' => (float) ($transaction->final_total ?? 0),
            'reference' => $transaction->payment_reference ?? null,
            'status' => $transaction->payment_status ?? 'completed',
        ];
    }
}