<?php

namespace App\EIS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\EIS\Models\EisInvoice;

class SyncOfflineInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $pending = EisInvoice::where('status', 'pending')->get();

        foreach ($pending as $invoice) {
            SubmitInvoiceJob::dispatch($invoice->id);
        }
    }
}