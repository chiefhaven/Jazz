<?php

namespace Modules\EIS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Transaction;
use Modules\EIS\Services\Sales\SaleSubmissionService;
use Modules\EIS\Models\EisSetting;

class RetryFailedSaleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SaleSubmissionService $service)
    {
        $failed = DB::table('eis_failed_transactions')
            ->where('next_retry_at', '<=', now())
            ->where('attempts', '<', 5)
            ->get();

        foreach ($failed as $row) {

            $transaction = Transaction::find($row->transaction_id);

            if (!$transaction) {
                continue;
            }

            $settings = EisSetting::where('business_id', $row->business_id)->first();

            if (!$settings) {
                continue;
            }

            try {
                $service->submit($transaction, $settings);

                // success → remove from retry queue
                DB::table('eis_failed_transactions')
                    ->where('id', $row->id)
                    ->delete();

            } catch (\Throwable $e) {

                DB::table('eis_failed_transactions')
                    ->where('id', $row->id)
                    ->update([
                        'attempts' => $row->attempts + 1,
                        'next_retry_at' => now()->addMinutes(pow(2, $row->attempts)),
                        'error_message' => $e->getMessage(),
                    ]);
            }
        }
    }
}