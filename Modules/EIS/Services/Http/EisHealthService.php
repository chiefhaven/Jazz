<?php

namespace Modules\EIS\Services\Http;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EisHealthService
{
    public function isOnline(?string $token): bool
    {
        if (empty($token)) {
            Log::warning('EIS health check skipped. Missing JWT token.');
            return false;
        }

        $cacheKey = 'eis.health.' . md5($token);

        return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($token) {

            try {

                $response = Http::connectTimeout(5)
                    ->timeout(10)
                    ->retry(2, 1000)
                    ->withToken($token)
                    ->acceptJson()
                    ->post(rtrim(config('eis.base_url'), '/') . '/utilities/ping');

                Log::info('EIS Ping Response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $response->successful();

            } catch (\Throwable $e) {

                Log::warning('EIS health check failed', [
                    'message' => $e->getMessage(),
                ]);

                return false;
            }
        });
    }

    public function clearCache(?string $token = null): void
    {
        if ($token) {
            Cache::forget('eis.health.' . md5($token));
        } else {
            Cache::flush();
        }
    }
}