<?php

namespace Modules\EIS\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Jobs\DispatchAllUnsubmittedSalesJob;
use Modules\EIS\Models\EisSetting;

class DispatchAllUnsubmittedSalesCommand extends Command
{
    protected $signature = 'eis:dispatch-unsubmitted
                            {--business-id= : Business ID to process}
                            {--chunk-size=50 : Number of transactions per chunk}
                            {--date-from= : Start date (YYYY-MM-DD)}
                            {--date-to= : End date (YYYY-MM-DD)}
                            {--dry-run : Show what would be dispatched without actually dispatching}';

    protected $description = 'Dispatch all unsubmitted sales to EIS';

    public function handle()
    {
        $businessId = $this->option('business-id');
        $chunkSize = (int) $this->option('chunk-size');
        $dateFrom = $this->option('date-from');
        $dateTo = $this->option('date-to');
        $dryRun = $this->option('dry-run');

        $this->info('Starting dispatch of unsubmitted sales...');

        // Get unsubmitted transactions count
        $query = $this->buildTransactionQuery($businessId, $dateFrom, $dateTo);
        $total = $query->count();

        if ($total === 0) {
            $this->info('No unsubmitted transactions found.');
            return 0;
        }

        $this->info("Found {$total} unsubmitted transactions.");

        if ($dryRun) {
            $this->table(
                ['Transaction ID', 'Invoice No', 'Business ID', 'Date'],
                $query->limit(20)->get()->map(function ($transaction) {
                    return [
                        $transaction->id,
                        $transaction->invoice_no,
                        $transaction->business_id,
                        $transaction->transaction_date->format('Y-m-d H:i:s')
                    ];
                })->toArray()
            );

            if ($total > 20) {
                $this->info("... and {$total - 20} more transactions.");
            }

            $this->info('Dry run completed. No jobs were dispatched.');
            return 0;
        }

        $this->info('Dispatching jobs...');

        // Dispatch the job
        $job = new DispatchAllUnsubmittedSalesJob(
            $businessId,
            $chunkSize,
            $dateFrom,
            $dateTo
        );

        dispatch($job);

        $this->info("All {$total} unsubmitted transactions have been dispatched.");
        $this->info('Check the queue worker logs for progress.');

        return 0;
    }

    protected function buildTransactionQuery($businessId, $dateFrom, $dateTo)
    {
        $query = \App\Transaction::where('status', 'completed')
            ->whereNotNull('invoice_no')
            ->whereDoesntHave('eisSale', function ($query) {
                $query->where('status', 'submitted');
            });

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        if ($dateFrom) {
            $query->where('transaction_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('transaction_date', '<=', $dateTo);
        }

        $query->whereHas('business', function ($query) {
            $query->whereHas('eisSetting', function ($query) {
                $query->where('status', 1)
                    ->whereNotNull('tpin')
                    ->whereNotNull('device_id');
            });
        });

        return $query;
    }
}