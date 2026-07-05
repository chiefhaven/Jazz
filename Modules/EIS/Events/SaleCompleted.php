<?php

namespace Modules\EIS\Events;

use App\Transaction;
use AWS\CRT\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

class SaleCompleted
{
    public function __construct(
        public Transaction $transaction
    ) {
        FacadesLog::info('SaleCompleted event created for transaction ID: ' . $transaction->id);
    }
}