<?php

namespace App\EIS\Listeners;

use App\EIS\Events\TokenExpired;
use App\EIS\Models\EisLog;

class LogTokenExpired
{
    public function handle(TokenExpired $event): void
    {
        EisLog::create([
            'type' => 'auth',
            'action' => 'token_expired',
            'status' => 'error',
            'message' => $event->reason,
        ]);
    }
}