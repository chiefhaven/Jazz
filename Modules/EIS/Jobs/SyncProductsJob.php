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

    public function __construct(public int $businessId) {}

    public function handle(ProductSyncService $service)
    {
        $settings = EisSetting::where('business_id', $this->businessId)->first();

        Log::info('Product syncing');

        if (!$settings) {
            return;
        }

        $service->sync($settings, $this->businessId);
    }
}