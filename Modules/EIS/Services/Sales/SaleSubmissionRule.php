<?php

namespace Modules\EIS\Services\Sales;

use App\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Events\SaleCompleted;

/**
 * Decides which sales initiate an EIS submission.
 *
 * A final sell initiates it when it is paid in full, by cash or any other method.
 * Drafts, quotations, suspended sales, credit sales and partially paid sales (partial cash included) do not,
 * so they are neither submitted nor left behind as pending EIS rows (pending rows count against the offline
 * limits). A sale that becomes fully paid later is picked up by the hourly DispatchAllUnsubmittedSalesJob.
 */
class SaleSubmissionRule
{
    /**
     * Ask EIS to submit a sale that was just saved.
     *
     * Safe to call inside or after the sale's database transaction: SaleCompleted is fired once the
     * outermost transaction has committed, and never if it rolls back. DispatchEisSaleJob then applies
     * qualifies() to the stored sale, so callers do not have to check the payment themselves.
     *
     * A failure is only logged. The sale is already saved, EIS keeps the failed submission and the
     * scheduler retries it, so it must not turn a saved sale into an error for the cashier.
     */
    public static function initiate(Transaction $transaction): void
    {
        if (! static::isFinalSell($transaction)) {
            return;
        }

        DB::afterCommit(function () use ($transaction) {
            try {
                event(new SaleCompleted($transaction));
            } catch (\Throwable $e) {
                Log::error('EIS submission failed after sale commit', [
                    'transaction_id' => $transaction->id,
                    'message' => $e->getMessage(),
                ]);
            }
        });
    }

    /**
     * True when the sale must be submitted to EIS: a final sell that is paid in full.
     */
    public static function qualifies(Transaction $transaction): bool
    {
        return static::isFinalSell($transaction) && static::isPaid($transaction);
    }

    /**
     * True when the sale is paid in full (payment_status 'paid', which already nets off the change handed back).
     * A partial payment is not, whatever the payment method.
     */
    public static function isPaid(Transaction $transaction): bool
    {
        return $transaction->payment_status === 'paid';
    }

    /**
     * The same condition as isPaid(), as a constraint on a Transaction query.
     */
    public static function constrainPaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    protected static function isFinalSell(Transaction $transaction): bool
    {
        return $transaction->type === 'sell' && $transaction->status === 'final';
    }
}
