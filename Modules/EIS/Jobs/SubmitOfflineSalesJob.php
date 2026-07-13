<?php

namespace Modules\EIS\Jobs;

use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\EIS\Models\EisSale;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Sales\SaleSubmissionService;
use Modules\EIS\Exceptions\EisSaleException;

class SubmitOfflineSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;
    public $maxExceptions = 3;

    protected $transactionId;
    protected $businessId;

    public function __construct(int $transactionId, int $businessId)
    {
        $this->transactionId = $transactionId;
        $this->businessId = $businessId;
    }

    public function handle(SaleSubmissionService $submissionService): void
    {
        try {
            Log::info('SubmitOfflineSalesJob started', [
                'transaction_id' => $this->transactionId,
                'business_id' => $this->businessId,
                'attempt' => $this->attempts()
            ]);

            $transaction = Transaction::with([
                'business',
                'location',
                'contact',
                'sell_lines',
                'sell_lines.product',
                'sell_lines.variations',
                'sell_lines.modifiers',
            ])->find($this->transactionId);

            if (!$transaction) {
                Log::error('Transaction not found', [
                    'transaction_id' => $this->transactionId
                ]);
                $this->markJobAsFailed('Transaction not found');
                return;
            }

            $existingSale = EisSale::where('transaction_id', $this->transactionId)->first();
            if ($existingSale && $existingSale->status === 'submitted') {
                Log::info('Transaction already submitted to EIS', [
                    'transaction_id' => $this->transactionId
                ]);
                return;
            }

            $settings = EisSetting::where('business_id', $this->businessId)->first();
            if (!$settings) {
                Log::error('EIS settings not found', [
                    'business_id' => $this->businessId
                ]);
                $this->markJobAsFailed('EIS settings not found');
                return;
            }

            if (empty($settings->tpin) || empty($settings->device_id)) {
                Log::error('EIS settings incomplete', [
                    'business_id' => $this->businessId
                ]);
                $this->markJobAsFailed('EIS settings incomplete');
                return;
            }

            Log::info('Submitting transaction to EIS', [
                'transaction_id' => $this->transactionId,
                'invoice_no' => $transaction->invoice_no
            ]);

            $result = $submissionService->submit($transaction, $settings);

            Log::info('SubmitOfflineSalesJob completed successfully', [
                'transaction_id' => $this->transactionId,
                'eis_sale_id' => $result->id,
                'status' => $result->status
            ]);

        } catch (EisSaleException $e) {
            Log::error('EIS submission failed in job', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->markJobAsFailed($e->getMessage());
                return;
            }

            $this->release($this->backoff * $this->attempts());

        } catch (\Exception $e) {
            Log::error('Unexpected error in SubmitOfflineSalesJob', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts()
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->markJobAsFailed($e->getMessage());
                return;
            }

            $this->release($this->backoff * $this->attempts());
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SubmitOfflineSalesJob failed permanently', [
            'transaction_id' => $this->transactionId,
            'business_id' => $this->businessId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        try {
            $eisSale = EisSale::where('transaction_id', $this->transactionId)->first();
            if ($eisSale) {
                $eisSale->update([
                    'status' => 'failed',
                    'error_message' => $exception->getMessage(),
                ]);
            }

            DB::table('eis_failed_transactions')->insert([
                'business_id' => $this->businessId,
                'transaction_id' => $this->transactionId,
                'error_message' => $exception->getMessage(),
                'attempts' => $this->attempts(),
                'next_retry_at' => now()->addMinutes(30),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark EIS sale as failed', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function markJobAsFailed(string $errorMessage): void
    {
        try {
            $eisSale = EisSale::where('transaction_id', $this->transactionId)->first();
            if ($eisSale) {
                $eisSale->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                ]);
            }

            DB::table('eis_failed_transactions')->insert([
                'business_id' => $this->businessId,
                'transaction_id' => $this->transactionId,
                'error_message' => $errorMessage,
                'attempts' => $this->attempts(),
                'next_retry_at' => now()->addMinutes(30),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark job as failed', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function uniqueId(): string
    {
        return 'submit_offline_sale_' . $this->transactionId;
    }

    public function tags(): array
    {
        return [
            'submit_offline_sale',
            'transaction:' . $this->transactionId,
            'business:' . $this->businessId,
        ];
    }
}