<?php

namespace Modules\EIS\Listeners;

use Modules\EIS\Events\SaleCompleted;
use Modules\EIS\Jobs\SubmitSaleJob;

class DispatchEisSaleJob
{
    public function handle(SaleCompleted $event): void
    {
        SubmitSaleJob::dispatch($event->transaction->id)
            ->onQueue('eis-sales');
    }
}