<?php

namespace App\EIS\Controllers;

use Illuminate\Http\Request;
use App\EIS\Services\Sales\SalesService;
use App\EIS\DTO\Requests\InvoiceDTO;

class SalesController extends BaseController
{
    public function __construct(
        protected SalesService $service
    ) {}

    /**
     * Submit invoice
     */
    public function submit(Request $request)
    {
        $dto = new InvoiceDTO(
            invoiceNumber: $request->invoiceNumber,
            tin: $request->tin,
            customerName: $request->customerName,
            items: $request->items,
            totalAmount: $request->totalAmount,
            taxAmount: $request->taxAmount,
            paymentMethod: $request->paymentMethod ?? 'CASH',
        );

        return response()->json(
            $this->service->submitInvoice($dto)
        );
    }

    /**
     * Get invoice
     */
    public function show(Request $request)
    {
        return response()->json(
            $this->service->getInvoice($request->invoiceNumber)
        );
    }

    /**
     * Credit / Debit note
     */
    public function creditDebit(Request $request)
    {
        return response()->json(
            $this->service->processCreditDebit($request->all())
        );
    }

    /**
     * Cancel receipt
     */
    public function cancel(Request $request)
    {
        return response()->json(
            $this->service->cancelReceipt($request->receiptNumber)
        );
    }

    public function cancelled()
    {
        return response()->json(
            $this->service->cancelledReceipts()
        );
    }

    public function lastOnline()
    {
        return response()->json(
            $this->service->lastOnline()
        );
    }

    public function lastOffline()
    {
        return response()->json(
            $this->service->lastOffline()
        );
    }
}