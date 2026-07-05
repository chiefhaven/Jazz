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

    public $timeout = 120;

    public function handle(SaleSubmissionService $service)
    {
        // 1. LIMIT BATCH SIZE (CRITICAL)
        $failed = DB::table('eis_failed_transactions')
            ->where('next_retry_at', '<=', now())
            ->where('attempts', '<', 5)
            ->limit(50)
            ->lockForUpdate()
            ->get();

        foreach ($failed as $row) {

            // 2. Prevent race condition
            $updated = DB::table('eis_failed_transactions')
                ->where('id', $row->id)
                ->where('next_retry_at', '<=', now())
                ->update([
                    'next_retry_at' => now()->addMinutes(1), // temporary lock
                ]);

            if (!$updated) {
                continue;
            }

            try {
                $transaction = Transaction::find($row->transaction_id);

                if (!$transaction) {
                    DB::table('eis_failed_transactions')
                        ->where('id', $row->id)
                        ->delete();
                    continue;
                }

                $settings = EisSetting::where('business_id', $row->business_id)->first();

                if (!$settings) {
                    continue;
                }

                $service->submit($transaction, $settings);

                // SUCCESS → remove
                DB::table('eis_failed_transactions')
                    ->where('id', $row->id)
                    ->delete();

            } catch (\Throwable $e) {

                $attempts = $row->attempts + 1;

                DB::table('eis_failed_transactions')
                    ->where('id', $row->id)
                    ->update([
                        'attempts' => $attempts,
                        'next_retry_at' => now()->addMinutes(pow(2, $attempts)),
                        'error_message' => $e->getMessage(),
                    ]);
            }
        }
    }
}