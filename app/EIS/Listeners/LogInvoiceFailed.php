<?php

namespace App\EIS\Listeners;

use App\EIS\Events\InvoiceFailed;
use App\EIS\Models\EisLog;

class LogInvoiceFailed
{
    public function handle(InvoiceFailed $event): void
    {
        EisLog::create([
            'type' => 'invoice',
            'action' => 'failed',
            'reference' => $event->invoiceNumber,
            'status' => 'error',
            'message' => $event->error,
        ]);
    }
}