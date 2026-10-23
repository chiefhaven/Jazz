<?php

namespace Modules\EIS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Sync\ProductSyncService;

class SyncProductsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $businessId, public ?int $terminalId = null) {}

    public function handle(ProductSyncService $service)
    {
        Log::info('Products syncing');
        $settings = \Modules\EIS\Services\Terminal\TerminalContext::settings($this->businessId, null, $this->terminalId);
        $service->sync($settings, $this->businessId);
    }
}