<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSale;

class SaleResponseService
{
    /**
     * Handle successful EIS response
     */
    public function handle(Transaction $transaction, array $response): void
    {
        $eisSale = EisSale::where('transaction_id', $transaction->id)->first();

        if (!$eisSale) {
            Log::warning('EIS sale record not found', [
                'transaction_id' => $transaction->id,
            ]);
            return;
        }

        // -------------------------
        // NORMALIZED RESPONSE
        // -------------------------
        $normalized = $this->normalize($response);

        // -------------------------
        // UPDATE EIS TRACKING
        // -------------------------
        $eisSale->update([
            'status' => 'completed',
            'response_payload' => $response,

            'fiscal_invoice_number' => $normalized['fiscal_invoice_number'],
            'receipt_number' => $normalized['receipt_number'],
            'receipt_signature' => $normalized['signature'],
            'qr_code' => $normalized['qr_code'],
            'submitted_at' => now(),
        ]);

        // -------------------------
        // UPDATE POS TRANSACTION
        // -------------------------
        $transaction->update([
            'is_fiscalized' => 1,
            'fiscal_invoice_number' => $normalized['fiscal_invoice_number'],
            'receipt_number' => $normalized['receipt_number'],
        ]);

        // -------------------------
        // LOG SUCCESS
        // -------------------------
        Log::info('EIS sale completed successfully', [
            'transaction_id' => $transaction->id,
            'fiscal_invoice_number' => $normalized['fiscal_invoice_number'],
        ]);
    }

    /**
     * Normalize EIS response structure
     */
    private function normalize(array $response): array
    {
        return [
            'fiscal_invoice_number' => $response['fiscalInvoiceNumber']
                ?? $response['invoiceNumber']
                ?? null,

            'receipt_number' => $response['receiptNumber']
                ?? $response['receiptNo']
                ?? null,

            'signature' => $response['signature']
                ?? $response['offlineSignature']
                ?? null,

            'qr_code' => $response['qrCode']
                ?? $response['qr']
                ?? null,
        ];
    }
}