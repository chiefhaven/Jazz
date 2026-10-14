<?php

namespace Modules\EIS\Services\Http;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EisHealthService
{
    public function isOnline(int $businessId, string $token): bool
    {
        $base = rtrim(trim((string) config('eis.base_url')), '/');
        // Deployments configure either the host or the complete API base.
        $base = preg_replace('~/api/v1$~', '', $base);
        $token = trim($token);
        if ($base === '' || $token === '') {
            return false;
        }

        $url = $base.'/api/v1/utilities/ping';
        // Ignore legacy eight-hour entries; credential/host changes must recheck.
        $cacheKey = "eis.health.v2.{$businessId}";
        $identity = hash('sha256', $url."\0".$token);
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ($cached['identity'] ?? null) === $identity
            && is_bool($cached['online'] ?? null)) {
            return $cached['online'];
        }

        try {
            $response = Http::acceptJson()->withToken($token)
                ->connectTimeout(3)->timeout(5)
                ->withOptions(['allow_redirects' => false])
                ->post($url);
            $body = $response->json();
            $online = $response->successful() && is_array($body)
                && ($body['statusCode'] ?? null) === 1
                && empty($body['errors'])
                && is_array($body['data'] ?? null)
                && is_string($body['data']['serverDate'] ?? null)
                && $body['data']['serverDate'] !== '';
        } catch (\Throwable $e) {
            // Do not log credentials, response bodies, or request exception text.
            Log::warning('EIS health check failed', [
                'business_id' => $businessId,
                'exception' => get_class($e),
            ]);
            $online = false;
        }

        Cache::put($cacheKey, ['identity' => $identity, 'online' => $online], $online ? 60 : 15);

        return $online;
    }


    public function clearCache(int $businessId): void
    {
        Cache::forget("eis.health.v2.{$businessId}");
        Cache::forget("eis.health.{$businessId}");
        Cache::forget('eis_status_'.$businessId);
    }
}