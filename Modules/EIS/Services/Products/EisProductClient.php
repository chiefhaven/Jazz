<?php

namespace Modules\EIS\Services\Products;

use Illuminate\Support\Facades\Http;

class EisProductClient
{
    /** Shared with sync scoping: this is the exact value sent, with null-only fallback. */
    public static function requestSiteId($settings)
    {
        return $settings->site_id ?? $settings->branch_id;
    }

    public function fetch($settings, int $page = 1)
    {
        $response = Http::baseUrl(config('eis.base_url'))
            ->withToken($settings->jwt_token)
            ->post('/utilities/get-terminal-site-products', [
                'tin'    => $settings->tpin,
                'siteId' => self::requestSiteId($settings),
                'page'   => $page,
            ]);

        $response->throw();

        $payload = $response->json();
        if (!is_array($payload) || !array_key_exists('data', $payload) || !is_array($payload['data'])) {
            throw new \UnexpectedValueException('EIS product response has an invalid data collection.');
        }

        return $payload;
    }
}