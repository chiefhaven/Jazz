<?php

namespace Modules\EIS\Jobs;

use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Services\Sales\SaleSubmissionService;
use Modules\EIS\Models\EisSetting;

class SubmitSaleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $transactionId
    ) {}

    public function handle(SaleSubmissionService $service)
    {
        $transaction = Transaction::findOrFail($this->transactionId);

        $settings = EisSetting::where('business_id', $transaction->business_id)
            ->first();

            Log::info('SubmitSaleJob: Retrieved settings for business ID: ' . $transaction->business_id);

        if (!$settings) {
            return;
        }

        $service->submit($transaction, $settings);
    }
}