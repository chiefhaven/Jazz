<?php

namespace App\EIS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\EIS\Models\EisInvoice;

class RetryFailedInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $failed = EisInvoice::where('status', 'failed')
            ->where('attempts', '<', 3)
            ->get();

        foreach ($failed as $invoice) {
            SubmitInvoiceJob::dispatch($invoice->id);
        }
    }
}