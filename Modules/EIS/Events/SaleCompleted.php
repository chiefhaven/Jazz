<?php

namespace Modules\EIS\Events;

use App\Transaction;

class SaleCompleted
{
    public function __construct(
        public Transaction $transaction
    ) {}
}