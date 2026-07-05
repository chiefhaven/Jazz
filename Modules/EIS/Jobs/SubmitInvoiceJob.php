<?php
namespace Modules\EIS\Jobs;

use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Invoice\EisInvoiceService;

class SubmitInvoiceJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public $saleId, public $businessId) {}

    public function handle(EisInvoiceService $service)
    {
        $setting = EisSetting::where('business_id', $this->businessId)->first();

        if (!$setting) return;

        $sale = Transaction::find($this->saleId);

        if (!$sale) return;

        if ($setting->token_expires_at < now()) {
            app(\Modules\EIS\Services\Auth\EisAuthService::class)
                ->refresh($setting);
        }

        $response = $service->submit($sale, $setting);

        $sale->update([
            'eis_invoice_no' => $response['invoice_no'] ?? null,
            'eis_status' => 'synced',
        ]);
    }
}