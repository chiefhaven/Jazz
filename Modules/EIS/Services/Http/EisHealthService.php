<?php

namespace Modules\EIS\Services\Http;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EisHealthService
{
    public function isOnline(): bool
    {
        return Cache::remember('eis.health', now()->addSeconds(30), function () {

            try {

                $response = Http::connectTimeout(5)
                    ->timeout(10)
                    ->get(rtrim(config('eis.base_url'), '/') . '/utilities/ping');

                return $response->successful();

            } catch (\Throwable $e) {

                Log::warning('EIS health check failed', [
                    'error' => $e->getMessage(),
                ]);

                return false;
            }

        });
    }

    public function clearCache(): void
    {
        Cache::forget('eis.health');
    }
}