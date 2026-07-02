<?php
//use this in POS
$invoice = EisInvoice::create([
    'invoice_number' => $invoiceNumber,
    'tin' => $tin,
    'customer_name' => $customer,
    'payload' => [
        'items' => $items,
        'totalAmount' => $total,
        'taxAmount' => $tax,
        'paymentMethod' => 'CASH',
    ],
    'status' => 'pending',
]);

SubmitInvoiceJob::dispatch($invoice->id);