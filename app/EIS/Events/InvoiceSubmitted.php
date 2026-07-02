<?php

namespace App\EIS\Events;

class InvoiceSubmitted
{
    public function __construct(
        public string $invoiceNumber,
        public array $response
    ) {}
}