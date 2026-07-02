<?php

namespace App\EIS\Events;

class InvoiceFailed
{
    public function __construct(
        public string $invoiceNumber,
        public string $error
    ) {}
}