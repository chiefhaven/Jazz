<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSale;
use Modules\EIS\Services\Http\EisSaleClient;
use Modules\EIS\Exceptions\EisSaleException;

class SaleSubmissionService
{
    public function __construct(
        protected SaleTransformer $transformer,
        protected EisSaleClient $client
    ) {}

    /**
     * Submit transaction to EIS
     */
    public function submit(Transaction $transaction, object $settings): EisSale
    {
        return DB::transaction(function () use ($transaction, $settings) {

            // ----------------------------
            // IDEMPOTENCY CHECK
            // ----------------------------
            $existing = EisSale::where('transaction_id', $transaction->id)->first();

            if ($existing && in_array($existing->status, ['submitted'])) {
                return $existing;
            }

            // ----------------------------
            // TRACKING ROW
            // ----------------------------
            $eisSale = EisSale::updateOrCreate(
                ['transaction_id' => $transaction->id],
                [
                    'business_id' => $transaction->business_id,
                    'invoice_number' => $transaction->invoice_no,
                    'status' => 'pending',
                ]
            );

            // ensure payload exists for catch block
            $payload = [];

            try {

                // ----------------------------
                // TRANSFORM
                // ----------------------------
                $payload = $this->transformer->transform($transaction, $settings);

                $eisSale->update([
                    'request_payload' => $payload,
                ]);

                // ----------------------------
                // SUBMIT
                // ----------------------------
                $response = $this->client->submit($payload, $settings);

                // ----------------------------
                // SUCCESS UPDATE
                // ----------------------------
                $eisSale->update([
                    'status' => 'submitted',
                    'response_payload' => $response,
                    'submitted_at' => now(),

                    'fiscal_invoice_number' => $response['fiscalInvoiceNumber'] ?? null,
                    'receipt_number' => $response['receiptNumber'] ?? null,
                    'receipt_signature' => $response['signature'] ?? null,
                    'qr_code' => $response['qrCode'] ?? null,
                ]);

                return $eisSale;

            } catch (EisSaleException $e) {

                Log::error('EIS Sale Submission Failed', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);

                $eisSale->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                // -------------------------
                // RETRY STORAGE
                // -------------------------
                DB::table('eis_failed_transactions')->updateOrInsert(
                    [
                        'business_id' => $transaction->business_id,
                        'transaction_id' => $transaction->id,
                    ],
                    [
                        'payload' => json_encode($payload),
                        'error_message' => $e->getMessage(),
                        'attempts' => DB::raw('attempts + 1'),
                        'next_retry_at' => now()->addMinutes(5),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                throw $e;
            }
        });
    }
}