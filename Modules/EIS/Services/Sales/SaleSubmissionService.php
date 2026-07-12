<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSale;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Http\EisSaleClient;
use Modules\EIS\Exceptions\EisSaleException;
use Modules\EIS\Services\Sales\InvoiceNumberGenerator;
use Modules\EIS\Services\Sales\SaleResponseService;

class SaleSubmissionService
{
    public function __construct(
        protected SaleTransformer $transformer,
        protected EisSaleClient $client,
        protected SaleResponseService $responseService,
        protected InvoiceNumberGenerator $invoiceGenerator
    ) {}

    /**
     * Submit sale transaction to EIS.
     *
     * @param Transaction $transaction
     * @param object $settings
     * @return EisSale
     * @throws EisSaleException
     */
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
        // GET EIS SETTINGS
        // ----------------------------
        $eisSetting = EisSetting::where('business_id', $transaction->business_id)->first();
        if (!$eisSetting) {
            throw new EisSaleException('EIS settings not found for business: ' . $transaction->business_id);
        }

        // ----------------------------
        // GENERATE EIS INVOICE NUMBER
        // ----------------------------
        $eisInvoiceNumber = $this->generateEISInvoiceNumber($transaction, $eisSetting);

        // ----------------------------
        // CREATE/UPDATE TRACKING
        // ----------------------------
        $eisSale = EisSale::updateOrCreate(
            ['transaction_id' => $transaction->id],
            [
                'business_id' => $transaction->business_id,
                'invoice_number' => $transaction->invoice_no,
                'eis_invoice_number' => $eisInvoiceNumber,
                'status' => 'pending',
            ]
        );

        $payload = [];

        try {
            // ----------------------------
            // TRANSFORM WITH EIS INVOICE NUMBER
            // ----------------------------
            $payload = $this->transformer->transform($transaction, $settings, $eisInvoiceNumber);

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
                'eis_invoice_number' => $eisInvoiceNumber ?? null,
            ]);

            $eisSale->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // -------------------------
            // SAFE RETRY STORAGE
            // -------------------------
            $this->storeFailedTransaction($transaction, $payload, $e->getMessage());

            throw $e;
        }
    }

    /**
     * Generate EIS compliant invoice number.
     *
     * @param Transaction $transaction
     * @param EisSetting $setting
     * @return string
     * @throws EisSaleException
     */
    protected function generateEISInvoiceNumber(Transaction $transaction, EisSetting $setting): string
    {
        try {
            // Get terminal position from transaction or settings
            $terminalPosition = $transaction->terminal_position ?? 
                               $setting->terminal_position ?? 
                               $this->getTerminalPosition($transaction->business_id);

            // Generate invoice number
            $invoiceNumber = $this->invoiceGenerator->generateInvoiceNumber(
                $transaction->business_id,
                $terminalPosition
            );

            Log::debug('EIS invoice number generated', [
                'transaction_id' => $transaction->id,
                'invoice_number' => $invoiceNumber,
                'terminal_position' => $terminalPosition
            ]);

            return $invoiceNumber;

        } catch (\Exception $e) {
            Log::error('Failed to generate EIS invoice number', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            // Fallback: generate using transaction ID
            return $this->generateFallbackInvoiceNumber($transaction);
        }
    }

    /**
     * Get terminal position from terminal configuration.
     *
     * @param int $businessId
     * @return int
     */
    protected function getTerminalPosition(int $businessId): int
    {
        try {
            $terminal = \Modules\EIS\Models\EisTerminalConfiguration::where('configuration_id', $businessId)
                ->orderBy('id')
                ->first();

            return $terminal->terminal_position ?? 1;
        } catch (\Exception $e) {
            Log::warning('Failed to get terminal position, using default', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    /**
     * Generate fallback invoice number.
     *
     * @param Transaction $transaction
     * @return string
     */
    protected function generateFallbackInvoiceNumber(Transaction $transaction): string
    {
        // Format: FALLBACK-TPIN-YYYYMMDD-COUNT
        $setting = EisSetting::where('business_id', $transaction->business_id)->first();
        $tpin = $setting->tpin ?? '000000';
        $date = now()->format('Ymd');
        $count = str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
        
        return 'FALLBACK-' . $tpin . '-' . $date . '-' . $count;
    }

    /**
     * Store failed transaction for retry.
     *
     * @param Transaction $transaction
     * @param array $payload
     * @param string $errorMessage
     * @return void
     */
    protected function storeFailedTransaction(Transaction $transaction, array $payload, string $errorMessage): void
    {
        $failed = DB::table('eis_failed_transactions')
            ->where('business_id', $transaction->business_id)
            ->where('transaction_id', $transaction->id)
            ->first();

        if ($failed) {
            DB::table('eis_failed_transactions')
                ->where('id', $failed->id)
                ->update([
                    'attempts' => $failed->attempts + 1,
                    'next_retry_at' => $this->calculateNextRetry($failed->attempts + 1),
                    'error_message' => $errorMessage,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('eis_failed_transactions')->insert([
                'business_id' => $transaction->business_id,
                'transaction_id' => $transaction->id,
                'payload' => json_encode($payload),
                'error_message' => $errorMessage,
                'attempts' => 1,
                'next_retry_at' => now()->addMinutes(5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Calculate next retry time with exponential backoff.
     *
     * @param int $attempts
     * @return \DateTime
     */
    protected function calculateNextRetry(int $attempts): \DateTime
    {
        // Exponential backoff: 5, 10, 20, 40, 80 minutes
        $backoffMinutes = min(5 * pow(2, $attempts - 1), 1440); // Max 24 hours
        return now()->addMinutes($backoffMinutes);
    }

    /**
     * Retry a failed transaction.
     *
     * @param int $businessId
     * @param int $transactionId
     * @return array
     */
    public function retryFailedTransaction(int $businessId, int $transactionId): array
    {
        try {
            $failed = DB::table('eis_failed_transactions')
                ->where('business_id', $businessId)
                ->where('transaction_id', $transactionId)
                ->first();

            if (!$failed) {
                return [
                    'success' => false,
                    'message' => 'Failed transaction not found'
                ];
            }

            $transaction = Transaction::find($transactionId);
            if (!$transaction) {
                return [
                    'success' => false,
                    'message' => 'Transaction not found'
                ];
            }

            $settings = EisSetting::where('business_id', $businessId)->first();
            if (!$settings) {
                return [
                    'success' => false,
                    'message' => 'EIS settings not found'
                ];
            }

            // Retry submission
            $this->submit($transaction, $settings);

            // Remove from failed table on success
            DB::table('eis_failed_transactions')
                ->where('id', $failed->id)
                ->delete();

            return [
                'success' => true,
                'message' => 'Transaction retried successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to retry transaction', [
                'business_id' => $businessId,
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retry: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get pending failed transactions for retry.
     *
     * @param int $businessId
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getPendingRetries(int $businessId, int $limit = 100)
    {
        return DB::table('eis_failed_transactions')
            ->where('business_id', $businessId)
            ->where('next_retry_at', '<=', now())
            ->orderBy('next_retry_at')
            ->limit($limit)
            ->get();
    }
}