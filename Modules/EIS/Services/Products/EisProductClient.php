<?php

namespace Modules\EIS\Services\Products;

use Illuminate\Support\Facades\Http;

class EisProductClient
{
    public function fetch($settings, int $page = 1)
    {
        return Http::baseUrl(config('eis.base_url'))
            ->withToken($settings->jwt_token)
            ->post('/utilities/get-terminal-site-products', [
                'tin'    => $settings->tpin,
                'siteId' => $settings->site_id ?? $settings->branch_id,
                'page'   => $page,
            ])
            ->json();
    }
}