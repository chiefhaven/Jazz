<?php

namespace App\EIS\DTO\Requests;

class InvoiceDTO
{
    public function __construct(
        public string $invoiceNumber,
        public string $tin,
        public string $customerName,
        public array $items,
        public float $totalAmount,
        public float $taxAmount,
        public string $paymentMethod = 'CASH',
        public ?string $date = null
    ) {}

    public function toArray(): array
    {
        return [
            'invoiceNumber' => $this->invoiceNumber,
            'tin' => $this->tin,
            'customerName' => $this->customerName,
            'items' => $this->items,
            'totalAmount' => $this->totalAmount,
            'taxAmount' => $this->taxAmount,
            'paymentMethod' => $this->paymentMethod,
            'date' => $this->date ?? now()->toDateTimeString(),
        ];
    }
}