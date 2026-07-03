<?php
namespace Modules\EIS\Services\Http;

use Illuminate\Support\Facades\Http;
use Modules\EIS\Services\Security\EisSignatureService;

class EisHttpClient
{
    public function __construct(
        protected EisSignatureService $signer
    ) {}

    public function post($endpoint, $payload, $setting)
    {
        $timestamp = time();

        $signature = $this->signer->make(
            $payload,
            $setting->secret_key,
            $timestamp
        );

        return Http::baseUrl(config('eis.base_url'))
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