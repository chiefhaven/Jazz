<?php

namespace Modules\EIS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\EIS\Services\Stock\StockOutboundService;

class SubmitStockOperationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public int $operationId) {}

    public function handle(StockOutboundService $service): void
    {
        $service->submit($this->operationId);
    }

    public function failed(\Throwable $exception): void
    {
        app(StockOutboundService::class)->markJobFailureUnknown($this->operationId);
    }
}
