<?php

namespace App\EIS\Services\Sync;

use App\EIS\Models\EisInvoice;
use App\EIS\Jobs\SubmitInvoiceJob;

class SyncService
{
    /**
     * Sync all pending invoices
     */
    public function syncPendingInvoices(): void
    {
        EisInvoice::where('status', 'pending')
            ->each(function ($invoice) {
                SubmitInvoiceJob::dispatch($invoice->id);
            });
    }

    /**
     * Sync failed invoices
     */
    public function syncFailedInvoices(): void
    {
        EisInvoice::where('status', 'failed')
            ->where('attempts', '<', 3)
            ->each(function ($invoice) {
                SubmitInvoiceJob::dispatch($invoice->id);
            });
    }

    /**
     * Full reconciliation
     */
    public function reconcile(): void
    {
        $this->syncPendingInvoices();
        $this->syncFailedInvoices();
    }
}