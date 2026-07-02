<?php

namespace App\EIS\Controllers;

use Illuminate\Http\Request;
use App\EIS\Services\Utilities\UtilityService;
use App\Http\Controllers\Controller;

class UtilityController extends Controller
{
    public function __construct(
        protected UtilityService $service
    ) {}

    public function ping()
    {
        return response()->json($this->service->ping());
    }

    public function validateVat(Request $request)
    {
        return response()->json(
            $this->service->validateVatCertificate($request->vatNumber)
        );
    }

    public function validateAuth(Request $request)
    {
        return response()->json(
            $this->service->validateAuthorizationCode($request->authorizationCode)
        );
    }

    public function checkTin(Request $request)
    {
        return response()->json(
            $this->service->checkTinAuthorization($request->tin)
        );
    }

    public function terminalBlocking()
    {
        return response()->json($this->service->terminalBlockingMessage());
    }

    public function unblockStatus()
    {
        return response()->json($this->service->checkUnblockStatus());
    }

    public function products()
    {
        return response()->json($this->service->getTerminalProducts());
    }

    public function productStatus(Request $request)
    {
        return response()->json(
            $this->service->productStatus($request->productCode)
        );
    }

    public function uploadInventory(Request $request)
    {
        return response()->json(
            $this->service->uploadInitialInventory($request->items ?? [])
        );
    }
}