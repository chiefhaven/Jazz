<?php

namespace App\EIS\Services\Configuration;

use App\EIS\Services\Http\HttpClientService;

class ConfigurationService
{
    public function __construct(
        protected HttpClientService $http
    ) {}

    /**
     * Fetch latest configuration from EIS
     */
    public function getLatest(): array
    {
        return $this->http->get('configuration/latest');
    }

    /**
     * Request new terminal token
     */
    public function requestNewToken(): array
    {
        return $this->http->post('configuration/request-terminal-token');
    }
}