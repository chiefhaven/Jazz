<?php

namespace Modules\EIS\Listeners;

use Modules\EIS\Events\SaleCompleted;
use Modules\EIS\Jobs\SubmitSaleJob;
use Modules\EIS\Services\Sales\SaleSubmissionRule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class DispatchEisSaleJob implements ShouldQueue
{
    /**
     * Queue name can also be centralized in config
     */
    public string $queue = 'eis-sales';

    public function handle(SaleCompleted $event): void
    {
        // Only a final sell paid in full is submitted. Every place that fires SaleCompleted is covered here.
        if (! SaleSubmissionRule::qualifies($event->transaction)) {
            Log::info('DispatchEisSaleJob: Skipped transaction ID ' . $event->transaction->id . ', it is not a final sell paid in full');

            return;
        }

        SubmitSaleJob::dispatch($event->transaction->id)
            ->onQueue($this->queue);

            Log::info('DispatchEisSaleJob: Dispatched SubmitSaleJob for transaction ID: ' . $event->transaction->id);
    }
}