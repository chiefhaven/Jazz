<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Tax\TaxMappingService;

class SaleTransformer
{
    public function __construct(
        protected TaxMappingService $taxMapper
    ) {}

    public function transform(Transaction $transaction, EisSetting $settings): array
    {
        $transaction->loadMissing([
            'business',
            'location',
            'contact',
            'sell_lines.product',
            'tax',
        ]);

        $invoiceItems = [];
        $taxBreakdown = [];
        $totalVat = 0;

        foreach ($transaction->sell_lines as $index => $line) {

            $taxId = $line->tax_id;

            $taxRateId = $this->taxMapper->resolve(
                $settings->business_id,
                $taxId
            );

            $unitPrice = (float) $line->unit_price_inc_tax;
            $qty = (float) $line->quantity;

            $lineTotal = $unitPrice * $qty;
            $vat = (float) $line->item_tax;

            $invoiceItems[] = [
                'id' => $index + 1,
                'productCode' => $line->product->sku ?? 'N/A',
                'description' => $line->product->name ?? 'Unknown Product',
                'unitPrice' => $unitPrice,
                'quantity' => $qty,
                'discount' => (float) $line->line_discount_amount,
                'total' => $lineTotal,
                'totalVAT' => $vat,
                'taxRateId' => $taxRateId,
                'isProduct' => true,
            ];

            $key = $taxRateId;

            if (!isset($taxBreakdown[$key])) {
                $taxBreakdown[$key] = [
                    'rateId' => $taxRateId,
                    'taxableAmount' => 0,
                    'taxAmount' => 0,
                ];
            }

            $taxBreakdown[$key]['taxableAmount'] += $lineTotal;
            $taxBreakdown[$key]['taxAmount'] += $vat;

            $totalVat += $vat;
        }

        return [
            'invoiceHeader' => [
                'invoiceNumber' => $transaction->invoice_no,

                'invoiceDateTime' => optional($transaction->transaction_date)
                    ? $transaction->transaction_date
                    : now(),

                'sellerTIN' => $settings->tin,
                'buyerTIN' => optional($transaction->contact)->tax_number,
                'buyerName' => optional($transaction->contact)->name,
                'buyerAuthorizationCode' => '',

                'siteId' => $settings->site_id,

                'globalConfigVersion' => (int) $settings->global_config_version,
                'taxpayerConfigVersion' => (int) $settings->taxpayer_config_version,
                'terminalConfigVersion' => (int) $settings->terminal_config_version,

                'isExport' => false,
                'isReliefSupply' => false,

                'vat5CertificateDetails' => null,

                // FIX: should be actual payment method (cash/card/mobile)
                'paymentMethod' => $transaction->payment_method ?? 'CASH',
            ],

            'invoiceLineItems' => $invoiceItems,

            'invoiceSummary' => [
                'taxBreakDown' => array_values($taxBreakdown),
                'levyBreakDown' => [],
                'totalVAT' => $totalVat,
                'offlineSignature' => '',
                'invoiceTotal' => (float) $transaction->final_total,
                'amountTendered' => (float) $transaction->final_total,
            ],
        ];
    }
}