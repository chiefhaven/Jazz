<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Models\EisTaxRate;

class SaleTransformer
{
    public function __construct(
        protected EisTaxRate $taxRates
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

            $taxRateId = $this->taxRates->resolve(
                $settings->business_id,
                $line->tax_id
            );

            $unitPrice = (float) $line->unit_price_inc_tax;
            $quantity = (float) $line->quantity;
            $vat = (float) $line->item_tax;

            $lineTotal = $unitPrice * $quantity;

            $invoiceItems[] = [
                'id' => $index + 1,

                'productCode' => (string) (
                    $line->product->sku ?? 'N/A'
                ),

                'description' => (string) (
                    $line->product->name ?? 'Unknown Product'
                ),

                'unitPrice' => $unitPrice,
                'quantity' => $quantity,
                'discount' => (float) $line->line_discount_amount,
                'total' => $lineTotal,
                'totalVAT' => $vat,

                'taxRateId' => (string) $taxRateId,

                'isProduct' => true,
            ];


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


        return [

            'invoiceHeader' => [

                'invoiceNumber' => (string) $transaction->invoice_no,

                'invoiceDateTime' => $transaction->transaction_date
                    ? \Carbon\Carbon::parse($transaction->transaction_date)
                        ->toIso8601String()
                    : now()->toIso8601String(),


                'sellerTIN' => (string) $settings->tpin,

                'buyerTIN' => (string) optional($transaction->contact)
                    ->tax_number,

                'buyerName' => (string) optional($transaction->contact)
                    ->name,


                'buyerAuthorizationCode' => '',


                'siteId' => (string) (
                    $settings->branch_id 
                    ?? $settings->site_id
                ),


                'globalConfigVersion' => (int) 
                    $settings->global_config_version,

                'taxpayerConfigVersion' => (int) 
                    $settings->taxpayer_config_version,

                'terminalConfigVersion' => (int) 
                    $settings->terminal_config_version,


                'isExport' => false,

                'isReliefSupply' => false,


                'vat5CertificateDetails' => null,


                'paymentMethod' => strtoupper(
                    $transaction->payment_method ?? 'CASH'
                ),
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
}