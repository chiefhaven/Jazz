<?php

namespace Modules\EIS\Services\Product;

use Illuminate\Support\Facades\Http;

class EisProductClient
{
    public function fetch($settings, int $page = 1)
    {
        return Http::baseUrl(config('eis.base_url'))
            ->withToken($settings->jwt_token)
            ->get('/utilities/get-terminal-site-products', [
                'page' => $page
            ])
            ->json();
    }
}