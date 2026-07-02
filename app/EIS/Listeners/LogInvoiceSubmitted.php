<?php

namespace App\EIS\Listeners;

use App\EIS\Events\InvoiceSubmitted;
use App\EIS\Models\EisLog;

class LogInvoiceSubmitted
{
    public function handle(InvoiceSubmitted $event): void
    {
        EisLog::create([
            'type' => 'invoice',
            'action' => 'submitted',
            'reference' => $event->invoiceNumber,
            'response_payload' => json_encode($event->response),
            'status' => 'success',
            'message' => 'Invoice submitted successfully',
        ]);
    }
}