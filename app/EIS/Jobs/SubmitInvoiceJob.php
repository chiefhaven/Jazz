<?php

namespace App\EIS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\EIS\Models\EisInvoice;
use App\EIS\Services\Sales\SalesService;
use App\EIS\DTO\Requests\InvoiceDTO;
use Throwable;

class SubmitInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $invoiceId
    ) {}

    public function handle(SalesService $service): void
    {
        $invoice = EisInvoice::findOrFail($this->invoiceId);

        try {
            $dto = new InvoiceDTO(
                invoiceNumber: $invoice->invoice_number,
                tin: $invoice->tin,
                customerName: $invoice->customer_name,
                items: $invoice->payload['items'],
                totalAmount: $invoice->payload['totalAmount'],
                taxAmount: $invoice->payload['taxAmount'],
                paymentMethod: $invoice->payload['paymentMethod'] ?? 'CASH',
            );

            $response = $service->submitInvoice($dto);

            $invoice->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'last_error' => null,
            ]);

            event(new \App\EIS\Events\InvoiceSubmitted(
                $invoice->invoice_number,
                $response
            ));

        } catch (Throwable $e) {

            $invoice->increment('attempts');

            $invoice->update([
                'status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);

            // retry logic
            if ($invoice->attempts < 3) {
                self::dispatch($invoice->id)
                    ->delay(now()->addMinutes(2 * $invoice->attempts));
            }

            event(new \App\EIS\Events\InvoiceFailed(
                $invoice->invoice_number,
                $e->getMessage()
            ));
        }
    }
}