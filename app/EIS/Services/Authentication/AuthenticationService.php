<?php

namespace App\EIS\Services\Authentication;

use Illuminate\Support\Facades\Cache;
use App\EIS\Services\Http\HttpClientService;
use App\EIS\Exceptions\EisException;

class AuthenticationService
{
    protected HttpClientService $http;

    public function __construct(HttpClientService $http)
    {
        $this->http = $http;
    }

    /**
     * Get valid token (cached or fresh login)
     */
    public function getToken(): string
    {
        if (Cache::has('eis.token')) {
            return Cache::get('eis.token');
        }

        return $this->authenticate();
    }

    /**
     * Force authentication with EIS
     */
    public function authenticate(): string
    {
        $response = $this->http->post('onboarding/login', [
            'username'    => config('eis.username'),
            'password'    => config('eis.password'),
            'terminalId'  => config('eis.terminal_id'),
        ]);

        if (!isset($response['data']['token'])) {
            throw new EisException("Invalid authentication response from EIS");
        }

        $token = $response['data']['token'];

        // Cache token (default 50 minutes or API-defined expiry)
        $ttl = isset($response['data']['expiresIn'])
            ? $response['data']['expiresIn'] - 60
            : 3000;

        Cache::put('eis.token', $token, $ttl);

        return $token;
    }

    /**
     * Clear cached token (force re-login next request)
     */
    public function clearToken(): void
    {
        Cache::forget('eis.token');
    }
}