<?php

namespace Modules\EIS\Services\Http;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EisHealthService
{
    public function isOnline(int $businessId, string $token): bool
    {
        $cacheKey = "eis.health.{$businessId}";

        return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($token) {

            try {

                $response = Http::connectTimeout(10)
                    ->timeout(300)
                    ->retry(2, 5000)
                    ->acceptJson()
                    ->withToken($token)
                    ->post(rtrim(config('eis.base_url'), '/') . '/utilities/get-terminal-config');

                return $response->successful();

            } catch (\Throwable $e) {

                Log::warning('EIS health check failed', [
                    'error' => $e->getMessage(),
                ]);

                return false;
            }

        });
    }

    public function clearCache(int $businessId): void
    {
        Cache::forget("eis.health.{$businessId}");
    }
}