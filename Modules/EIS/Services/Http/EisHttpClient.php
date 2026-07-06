<?php
namespace Modules\EIS\Services\Http;

use Illuminate\Support\Facades\Http;
use Modules\EIS\Exceptions\EisSaleException;
use Modules\EIS\Services\Security\EisSignatureService;

class EisHttpClient
{   
    protected string $baseUrl;

    public function __construct(
        protected EisSignatureService $signer,
        protected EisHealthService $health

    ) {
        $this->baseUrl = rtrim(config('eis.base_url'), '/');
    }


    public function post($endpoint, $payload, $setting)
    {
        if (! $this->health->isOnline()) {
            throw new EisSaleException('EIS server is currently unavailable.');
        }

        $timestamp = time();

        $signature = $this->signer->make(
            $payload,
            $setting->secret_key,
            $timestamp
        );

        return Http::baseUrl($this->baseUrl)
            ->withToken($setting->jwt_token)
            ->withHeaders([
                'X-TPIN' => $setting->tpin,
                'X-SIGNATURE' => $signature,
                'X-TIMESTAMP' => $timestamp,
                'X-DEVICE-ID' => $setting->device_id,
            ])
            ->post($endpoint, $payload)
            ->json();
    }

    public function get($endpoint, $setting)
    {
        $timestamp = time();

        $signature = hash_hmac('sha256', $endpoint . $timestamp, $setting->secret_key);

        return Http::baseUrl($setting->base_url)
            ->withToken($setting->jwt_token)
            ->withHeaders([
                'X-TPIN' => $setting->tpin,
                'X-SIGNATURE' => $signature,
                'X-TIMESTAMP' => $timestamp,
            ])
            ->get($endpoint)
            ->json();
    }
}