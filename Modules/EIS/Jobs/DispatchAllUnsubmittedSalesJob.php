<?php

namespace Modules\EIS\Jobs;

use App\Transaction;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;

class DispatchAllUnsubmittedSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $tries = 3;
    public $backoff = 60;
    public $timeout = 3600;

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
            Log::info('DispatchAllUnsubmittedSalesJob started');

            $transactions = $this->getUnsubmittedTransactions();

            if ($transactions->isEmpty()) {
                Log::info('No unsubmitted transactions found');
                return;
            }

            $total = $transactions->count();
            Log::info("Found {$total} unsubmitted transactions");

            // Create batch jobs
            $batchJobs = [];
            foreach ($transactions->chunk($this->chunkSize) as $chunk) {
                $batchJobs[] = new ProcessSalesChunkJob($chunk->pluck('id')->toArray());
            }

            // Dispatch batch
            if (!empty($batchJobs)) {
                Bus::batch($batchJobs)
                    ->name('EIS Sale Submission Batch')
                    ->dispatch();
            }

            Log::info('DispatchAllUnsubmittedSalesJob completed', [
                'total_transactions' => $total,
                'total_batches' => count($batchJobs)
            ]);

        } catch (\Exception $e) {
            Log::error('DispatchAllUnsubmittedSalesJob failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function getUnsubmittedTransactions()
    {
        $query = Transaction::with(['business.eisSetting'])
            ->where('status', 'completed')
            ->whereNotNull('invoice_no')
            ->whereDoesntHave('eisSale', function ($query) {
                $query->where('status', 'submitted');
            });

        if ($this->businessId) {
            $query->where('business_id', $this->businessId);
        }

        if ($this->dateFrom) {
            $query->whereDate('transaction_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('transaction_date', '<=', $this->dateTo);
        }

        $query->whereHas('business', function ($query) {
            $query->whereHas('eisSetting', function ($query) {
                $query->where('status', 1)
                    ->whereNotNull('tpin')
                    ->whereNotNull('device_id')
                    ->whereNotNull('jw_token');
            });
        });

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

    public function failed(\Throwable $exception): void
    {
        Log::error('DispatchAllUnsubmittedSalesJob failed permanently', [
            'business_id' => $this->businessId,
            'error' => $exception->getMessage()
        ]);
    }
}

// New job to process chunk
class ProcessSalesChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionIds;

    public function __construct(array $transactionIds)
    {
        $this->transactionIds = $transactionIds;
    }

    public function handle()
    {
        foreach ($this->transactionIds as $transactionId) {
            $transaction = Transaction::find($transactionId);
            if ($transaction) {
                SubmitOfflineSalesJob::dispatch(
                    $transaction->id,
                    $transaction->business_id
                );
            }
        }
    }
}