<?php

namespace Modules\EIS\Services\Http;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EisHealthService
{
    protected Client $client;

    public function __construct()
    {
        $stack = HandlerStack::create();

        $stack->push(
            Middleware::retry(
                function (
                    int $retries,
                    $request,
                    $response = null,
                    $exception = null
                ) {
                    return $retries < 2 && (
                        $exception ||
                        ($response && $response->getStatusCode() >= 500)
                    );
                },
                function () {
                    return 5000; // milliseconds
                }
            )
        );


        $this->client = new Client([
            'handler' => $stack,
            'connect_timeout' => 1000,
            'timeout' => 10000,
        ]);
    }


    public function isOnline(int $businessId, string $token): bool
    {
        $cacheKey = "eis.health.{$businessId}";


        return Cache::remember(
            $cacheKey,
            now()->addSeconds(30),
            function () use ($token) {

                try {

                    $response = $this->client->post(
                        rtrim(config('eis.base_url'), '/') . '/utilities/ping',
                        [
                            'headers' => [
                                'Accept' => 'application/json',
                                'Authorization' => "Bearer {$token}",
                            ],

                            // Prevent Guzzle throwing exceptions
                            // on HTTP 4xx/5xx
                            'http_errors' => false,
                        ]
                    );


                    return $response->getStatusCode() >= 200
                        && $response->getStatusCode() < 300;


                } catch (\Throwable $e) {

                    Log::warning('EIS health check failed', [
                        'business_id' => "businessId",
                        'error' => $e->getMessage(),
                    ]);


                    return false;
                }

            }
        );
    }


    public function clearCache(int $businessId): void
    {
        Cache::forget("eis.health.{$businessId}");
    }
}