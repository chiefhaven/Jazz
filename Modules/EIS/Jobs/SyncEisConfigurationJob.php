<?php

namespace Modules\EIS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\EIS\Services\Configuration\ConfigurationSyncService;


class SyncEisConfigurationJob implements ShouldQueue
{

    use Dispatchable, Queueable;


    public function __construct(
        public int $businessId,
        public string $token
    ){}



    public function handle(
        ConfigurationSyncService $service
    ){

        $service->sync(
            $this->businessId,
            $this->token
        );

    }

}