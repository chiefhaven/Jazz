<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSale;
use Modules\EIS\Services\Http\EisSaleClient;
use Modules\EIS\Exceptions\EisSaleException;
use Modules\EIS\Services\Sales\SaleResponseService;

class SaleSubmissionService
{
    public function __construct(
        protected SaleTransformer $transformer,
        protected EisSaleClient $client,
        protected SaleResponseService $responseService
    ) {}

    public function submit(Transaction $transaction, object $settings): EisSale
    {
        // ----------------------------
        // IDEMPOTENCY CHECK (BEFORE DB TX)
        // ----------------------------
        $existing = EisSale::where('transaction_id', $transaction->id)->first();

        if ($existing && $existing->status === 'submitted') {
            return $existing;
        }

        // ----------------------------
        // CREATE/UPDATE TRACKING
        // ----------------------------
        $eisSale = EisSale::updateOrCreate(
            ['transaction_id' => $transaction->id],
            [
                'business_id' => $transaction->business_id,
                'invoice_number' => $transaction->invoice_no,
                'status' => 'pending',
            ]
        );

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
            // SUBMIT (OUTSIDE DB TRANSACTION)
            // ----------------------------
            $response = $this->client->submit($payload, $settings);

            // ----------------------------
            // SINGLE SOURCE OF TRUTH UPDATE
            // ----------------------------
            $this->responseService->handle($transaction, $response);

            // ONLY mark EIS record
            $eisSale->update([
                'status' => 'submitted',
                'response_payload' => $response,
                'submitted_at' => now(),
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
            // SAFE RETRY STORAGE
            // -------------------------
            $failed = DB::table('eis_failed_transactions')
                ->where('business_id', $transaction->business_id)
                ->where('transaction_id', $transaction->id)
                ->first();

            if ($failed) {
                DB::table('eis_failed_transactions')
                    ->where('id', $failed->id)
                    ->update([
                        'attempts' => $failed->attempts + 1,
                        'next_retry_at' => now()->addMinutes(5),
                        'error_message' => $e->getMessage(),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('eis_failed_transactions')->insert([
                    'business_id' => $transaction->business_id,
                    'transaction_id' => $transaction->id,
                    'payload' => json_encode($payload),
                    'error_message' => $e->getMessage(),
                    'attempts' => 1,
                    'next_retry_at' => now()->addMinutes(5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            throw $e;
        }
    }
}