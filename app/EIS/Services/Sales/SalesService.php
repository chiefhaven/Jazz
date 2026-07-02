<?php

namespace App\EIS\Services\Sales;

use App\EIS\Services\Http\HttpClientService;
use App\EIS\DTO\Requests\InvoiceDTO;
use App\EIS\Exceptions\EisException;

class SalesService
{
    public function __construct(
        protected HttpClientService $http
    ) {}

    /**
     * Submit invoice to EIS
     */
    public function submitInvoice(InvoiceDTO $dto): array
    {
        $response = $this->http->post('sales/submit-sale', $dto->toArray());

        if (!isset($response['data'])) {
            throw new EisException("Invoice submission failed");
        }

        return $response['data'];
    }

    /**
     * Retrieve invoice from EIS
     */
    public function getInvoice(string $invoiceNumber): array
    {
        return $this->http->post('sales/get-invoice', [
            'invoiceNumber' => $invoiceNumber
        ]);
    }

    /**
     * Credit / Debit note
     */
    public function processCreditDebit(array $payload): array
    {
        return $this->http->post('sales/process-credit-debit-note', $payload);
    }

    /**
     * Cancel receipt
     */
    public function cancelReceipt(string $receiptNumber): array
    {
        return $this->http->post('sales/cancel-receipt', [
            'receiptNumber' => $receiptNumber
        ]);
    }

    /**
     * Get cancelled receipts
     */
    public function cancelledReceipts(): array
    {
        return $this->http->get('sales/get-cancelled-receipts');
    }

    /**
     * Last online transaction
     */
    public function lastOnline(): array
    {
        return $this->http->get('sales/last-submitted-online-transaction');
    }

    /**
     * Last offline transaction
     */
    public function lastOffline(): array
    {
        return $this->http->get('sales/last-submitted-offline-transaction');
    }
}