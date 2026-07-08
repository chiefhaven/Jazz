<?php

namespace Modules\EIS\Console\Commands;

use Illuminate\Console\Command;
use Modules\EIS\Jobs\SyncEisConfigurationJob;


class SyncEisConfiguration extends Command
{

    protected $signature =
        'eis:sync-config {businessId} {token}';


    protected $description =
        'Sync latest EIS configuration';


    public function handle()
    {

        SyncEisConfigurationJob::dispatch(
            $this->argument('businessId'),
            $this->argument('token')
        );


        $this->info(
            'EIS configuration sync started'
        );

    }

}