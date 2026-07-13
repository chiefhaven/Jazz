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

class DispatchAllUnsubmittedSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;
    public $timeout = 3600; // 1 hour for large datasets

    protected $businessId;
    protected $chunkSize;
    protected $dateFrom;
    protected $dateTo;

    public function __construct(
        ?int $businessId = null,
        int $chunkSize = 50,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ) {
        $this->businessId = $businessId;
        $this->chunkSize = $chunkSize;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function handle(): void
    {
        try {
            Log::info('DispatchAllUnsubmittedSalesJob started', [
                'business_id' => $this->businessId,
                'chunk_size' => $this->chunkSize,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo
            ]);

            // Get all unsubmitted transactions
            $transactions = $this->getUnsubmittedTransactions();

            if ($transactions->isEmpty()) {
                Log::info('No unsubmitted transactions found');
                return;
            }

            $total = $transactions->count();
            Log::info("Found {$total} unsubmitted transactions");

            // Process in chunks
            $chunks = $transactions->chunk($this->chunkSize);
            $totalChunks = $chunks->count();

            foreach ($chunks as $index => $chunk) {
                Log::debug('Processing chunk', [
                    'chunk' => $index + 1,
                    'total_chunks' => $totalChunks,
                    'chunk_size' => $chunk->count()
                ]);

                foreach ($chunk as $transaction) {
                    SubmitOfflineSalesJob::dispatch(
                        $transaction->id,
                        $transaction->business_id
                    );
                }

                if ($index < $totalChunks - 1) {
                    usleep(100000); // 0.1 second delay
                }
            }

            Log::info('DispatchAllUnsubmittedSalesJob completed', [
                'total_transactions' => $total,
                'total_chunks' => $totalChunks
            ]);

        } catch (\Exception $e) {
            Log::error('DispatchAllUnsubmittedSalesJob failed', [
                'business_id' => $this->businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Get all unsubmitted transactions.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getUnsubmittedTransactions()
    {
        $query = Transaction::with(['business'])
            ->where('status', 'completed')
            ->whereNotNull('invoice_no')
            ->whereDoesntHave('eisSale', function ($query) {
                $query->where('status', 'submitted');
            });

        // Filter by business
        if ($this->businessId) {
            $query->where('business_id', $this->businessId);
        }

        // Filter by date range
        if ($this->dateFrom) {
            $query->where('transaction_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('transaction_date', '<=', $this->dateTo);
        }

        // Only include transactions that have EIS settings
        $query->whereHas('business', function ($query) {
            $query->whereHas('eisSetting', function ($query) {
                $query->where('status', 1)
                    ->whereNotNull('tpin')
                    ->whereNotNull('device_id');
            });
        });

        // Order by date (oldest first)
        $query->orderBy('transaction_date', 'asc');

        return $query->get();
    }

    public function tags(): array
    {
        return [
            'dispatch_all_unsubmitted_sales',
            'business:' . ($this->businessId ?? 'all'),
        ];
    }
}