<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Modules\EIS\Models\EisSetting;

class SaleTransformer
{
    public function transform(Transaction $transaction, EisSetting $settings): array
    {
        $transaction->loadMissing([
            'business',
            'location',
            'contact',
            'sell_lines.product',
            'sell_lines.variations',
            'tax',
        ]);

        $invoiceItems = [];

        $taxBreakdown = [];

        $totalVat = 0;

        foreach ($transaction->sell_lines as $index => $line) {

            $taxRate = $line->tax_id ?? '';

            $vat = (float) $line->item_tax;

            $lineTotal = (float) $line->quantity * (float) $line->unit_price_inc_tax;

            $invoiceItems[] = [

                'id' => $index + 1,

                'productCode' => optional($line->product)->sku,

                'description' => optional($line->product)->name,

                'unitPrice' => (float) $line->unit_price_inc_tax,

                'quantity' => (float) $line->quantity,

                'discount' => (float) $line->line_discount_amount,

                'total' => $lineTotal,

                'totalVAT' => $vat,

                'taxRateId' => (string) $taxRate,

                'isProduct' => true,
            ];

            if (!isset($taxBreakdown[$taxRate])) {

                $taxBreakdown[$taxRate] = [

                    'rateId' => (string) $taxRate,

                    'taxableAmount' => 0,

                    'taxAmount' => 0,
                ];
            }

            $taxBreakdown[$taxRate]['taxableAmount'] += $lineTotal;

            $taxBreakdown[$taxRate]['taxAmount'] += $vat;

            $totalVat += $vat;
        }

        return [

            'invoiceHeader' => [

                'invoiceNumber' => $transaction->invoice_no,

                'invoiceDateTime' => $transaction->transaction_date->toISOString(),

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

                'paymentMethod' => $transaction->payment_status,
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