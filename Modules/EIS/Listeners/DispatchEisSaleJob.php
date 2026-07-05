<?php

namespace Modules\EIS\Listeners;

use Modules\EIS\Events\SaleCompleted;
use Modules\EIS\Jobs\SubmitSaleJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class DispatchEisSaleJob implements ShouldQueue
{
    /**
     * Queue name can also be centralized in config
     */
    public string $queue = 'eis-sales';

    public function handle(SaleCompleted $event): void
    {
        SubmitSaleJob::dispatch($event->transaction->id)
            ->onQueue($this->queue);
    }
}