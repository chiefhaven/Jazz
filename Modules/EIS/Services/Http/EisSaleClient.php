<?php

namespace Modules\EIS\Services\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Exceptions\EisSaleException;

class EisSaleClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('eis.base_url'), '/');
    }

    /**
     * Submit invoice to MRA EIS
     */
    public function submit(array $payload, object $settings): array
    {
        try {

            $url = $this->baseUrl . '/sales/submit-sales-transaction'; // adjust if different endpoint

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $settings->jwt_token,
                    'X-Secret-Key' => $settings->secret_key,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if (!$response->successful()) {

                Log::error('EIS invoice submission failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload,
                ]);

                throw new EisSaleException(
                    'EIS submission failed: ' . $response->body()
                );
            }

            return $response->json();

        } catch (\Throwable $e) {

            Log::error('EIS HTTP client error', [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            throw new EisSaleException($e->getMessage(), 0, $e);
        }
    }
}