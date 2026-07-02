<?php

namespace App\EIS\Listeners;

use App\EIS\Events\TerminalActivated;
use App\EIS\Models\EisLog;

class LogTerminalActivated
{
    public function handle(TerminalActivated $event): void
    {
        EisLog::create([
            'type' => 'onboarding',
            'action' => 'activated',
            'reference' => $event->terminalId,
            'status' => 'success',
            'message' => 'Terminal activated successfully',
        ]);
    }
}