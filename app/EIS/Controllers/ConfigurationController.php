<?php

namespace App\EIS\Controllers;

use App\EIS\Services\Configuration\ConfigurationService;

class ConfigurationController extends BaseController
{
    public function __construct(
        protected ConfigurationService $service
    ) {}

    public function latest()
    {
        return response()->json($this->service->getLatest());
    }

    public function requestToken()
    {
        return response()->json($this->service->requestNewToken());
    }
}