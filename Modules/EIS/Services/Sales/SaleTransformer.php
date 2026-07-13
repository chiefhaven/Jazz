<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use App\TransactionSellLine;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Models\EisTaxRate;

class SaleTransformer
{
    public function __construct(
        protected EisTaxRate $taxRateModel,
        protected OfflineSignatureService $signatureService
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
        // Load relationships
        $transaction->loadMissing([
            'business',
            'location',
            'contact',
            'sell_lines',
            'sell_lines.product',
            'sell_lines.variations',
            'sell_lines.modifiers',
        ]);

        $invoiceItems = [];
        $taxBreakdown = [];
        $totalVat = 0;
        $subtotal = 0;
        $totalItems = 0;

        foreach ($transaction->sell_lines as $index => $line) {
            // Get tax rate ID from EIS tax rates
            $taxRateId = $this->getTaxRateId($settings->business_id, $line);

            // Calculate values
            $unitPrice = (float) ($line->unit_price_inc_tax ?? $line->unit_price ?? 0);
            $quantity = (float) ($line->quantity ?? 1);
            $vat = (float) ($line->item_tax ?? 0);
            $discount = (float) ($line->line_discount_amount ?? 0);
            $lineTotal = $unitPrice * $quantity;
            $subtotal += $lineTotal;
            $totalItems += $quantity;

            // Get product details
            $productCode = $this->getProductCode($line);
            $productName = $this->getProductName($line);

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

        // Get buyer information
        $buyerInfo = $this->getBuyerInfo($transaction);

        // Build invoice summary
        $invoiceSummary = [
            'taxBreakDown' => array_values($taxBreakdown),
            'levyBreakDown' => [],
            'totalVAT' => (float) $totalVat,
            'offlineSignature' => '',
            'invoiceTotal' => (float) ($transaction->final_total ?? $transaction->total ?? 0),
            'amountTendered' => (float) ($transaction->final_total ?? $transaction->total ?? 0),
        ];

        // Generate offline signature
        $offlineSignatureData = $this->generateOfflineSignature(
            $settings,
            $transaction,
            $eisInvoiceNumber,
            $invoiceSummary,
            $totalItems
        );

        // Add offline signature to summary
        $invoiceSummary['offlineSignature'] = $offlineSignatureData['offlineDataSignature'] ?? '';
        $invoiceSummary['validationURL'] = $offlineSignatureData['validationURL'] ?? '';

        return [
            'invoiceHeader' => [
                'invoiceNumber' => (string) $eisInvoiceNumber,
                'invoiceDateTime' => $transaction->transaction_date
                    ? \Carbon\Carbon::parse($transaction->transaction_date)->toIso8601String()
                    : now()->toIso8601String(),
                'sellerTIN' => (string) ($settings->tpin ?? ''),
                'buyerTIN' => (string) ($buyerInfo['tpin'] ?? ''),
                'buyerName' => (string) ($buyerInfo['name'] ?? ''),
                'buyerAuthorizationCode' => (string) ($buyerInfo['authorization_code'] ?? ''),
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
            'invoiceSummary' => $invoiceSummary,
            'offlineSignatureData' => $offlineSignatureData,
        ];
    }

    /**
     * Generate offline signature data.
     *
     * @param EisSetting $settings
     * @param Transaction $transaction
     * @param string $eisInvoiceNumber
     * @param array $invoiceSummary
     * @param int $totalItems
     * @return array
     */
    protected function generateOfflineSignature(
        EisSetting $settings,
        Transaction $transaction,
        string $eisInvoiceNumber,
        array $invoiceSummary,
        int $totalItems
    ): array {
        try {
            // Parse invoice number to get components
            $parsedInvoice = $this->parseInvoiceNumber($eisInvoiceNumber);
            
            // Get taxpayer ID and terminal position
            $taxpayerId = $settings->tpin ?? '000000';
            $terminalPosition = (int) ($parsedInvoice['terminal_position'] ?? 1);
            $transactionCount = (int) ($parsedInvoice['count'] ?? 1);
            
            // Prepare request data for signature
            $requestData = [
                'transactiondate' => $transaction->transaction_date 
                    ? $transaction->transaction_date->toISOString() 
                    : now()->toISOString(),
                'transactionCount' => $transactionCount,
                'NumItems' => $totalItems,
                'InvoiceTotal' => (float) ($invoiceSummary['invoiceTotal'] ?? 0),
                'VATAmount' => (float) ($invoiceSummary['totalVAT'] ?? 0),
            ];
            
            // Generate offline signature
            $result = $this->signatureService->generateInvoiceResponse(
                $taxpayerId,
                $terminalPosition,
                $requestData,
                $settings->secret_key ?? ''
            );
            
            Log::debug('Offline signature generated for transaction', [
                'transaction_id' => $transaction->id,
                'invoice_number' => $eisInvoiceNumber,
                'validation_url' => $result['validationURL'] ?? ''
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::warning('Failed to generate offline signature', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'offlineDataSignature' => '',
                'validationURL' => '',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Parse invoice number.
     *
     * @param string $invoiceNumber
     * @return array
     */
    protected function parseInvoiceNumber(string $invoiceNumber): array
    {
        try {
            $generator = app(InvoiceNumberGenerator::class);
            $parsed = $generator->parseInvoiceNumber($invoiceNumber);
            
            if ($parsed) {
                return $parsed;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to parse invoice number', [
                'invoice_number' => $invoiceNumber,
                'error' => $e->getMessage()
            ]);
        }
        
        return [
            'taxpayer_id' => '',
            'terminal_position' => 1,
            'julian_date' => '',
            'count' => 1,
        ];
    }

    /**
     * Get product code from sell line.
     *
     * @param TransactionSellLine $line
     * @return string
     */
    protected function getProductCode(TransactionSellLine $line): string
    {
        try {
            if ($line->product) {
                return $line->product->sku ?? $line->product->id ?? 'N/A';
            }
            return $line->product_id ?? 'N/A';
        } catch (\Exception $e) {
            return $line->product_id ?? 'N/A';
        }
    }

    /**
     * Get product name from sell line.
     *
     * @param TransactionSellLine $line
     * @return string
     */
    protected function getProductName(TransactionSellLine $line): string
    {
        try {
            if ($line->product) {
                return $line->product->name ?? $line->product_name ?? 'Unknown Product';
            }
            return $line->product_name ?? 'Unknown Product';
        } catch (\Exception $e) {
            return $line->product_name ?? 'Unknown Product';
        }
    }

    /**
     * Get buyer information from transaction.
     *
     * @param Transaction $transaction
     * @return array
     */
    protected function getBuyerInfo(Transaction $transaction): array
    {
        try {
            if ($transaction->contact) {
                return [
                    'name' => $transaction->contact->name ?? 'Walk-in Customer',
                    'tpin' => $transaction->contact->tax_number ?? '',
                    'authorization_code' => $transaction->contact->authorization_code ?? '',
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get buyer info', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }

        return [
            'name' => $transaction->customer_name ?? 'Walk-in Customer',
            'tpin' => $transaction->customer_tpin ?? '',
            'authorization_code' => '',
        ];
    }

    /**
     * Get EIS tax rate ID from transaction line.
     *
     * @param int $businessId
     * @param TransactionSellLine $line
     * @return string
     */
    protected function getTaxRateId(int $businessId, TransactionSellLine $line): string
    {
        try {
            // Try to find tax rate by tax_id if available
            if (!empty($line->tax_id)) {
                $taxRate = $this->taxRateModel
                    ->where('business_id', $businessId)
                    ->where('tax_rate_id', $line->tax_id)
                    ->first();

                if ($taxRate) {
                    return $taxRate->tax_rate_id;
                }
            }

            // Try to find by tax rate value
            $unitPrice = (float) ($line->unit_price_inc_tax ?? $line->unit_price ?? 0);
            $quantity = (float) ($line->quantity ?? 1);
            $vat = (float) ($line->item_tax ?? 0);
            
            if ($unitPrice > 0 && $quantity > 0) {
                $calculatedRate = ($vat / ($unitPrice * $quantity)) * 100;
                
                if ($calculatedRate > 0) {
                    $taxRate = $this->taxRateModel
                        ->where('business_id', $businessId)
                        ->where('is_activated', true)
                        ->where('rate', '>=', $calculatedRate - 0.01)
                        ->where('rate', '<=', $calculatedRate + 0.01)
                        ->first();

                    if ($taxRate) {
                        return $taxRate->tax_rate_id;
                    }
                }
            }

            // Try to find by product tax
            if (!empty($line->product_tax)) {
                $taxRate = $this->taxRateModel
                    ->where('business_id', $businessId)
                    ->where('tax_rate_id', $line->product_tax)
                    ->first();

                if ($taxRate) {
                    return $taxRate->tax_rate_id;
                }
            }

            // Default to standard rate or first activated rate
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
                'line_id' => $line->id,
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
}