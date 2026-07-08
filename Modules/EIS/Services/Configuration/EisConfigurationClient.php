<?php

namespace Modules\EIS\Services\Configuration;

use AWS\CRT\Log;
use Illuminate\Support\Facades\Http;
use Modules\EIS\Exceptions\EisException;

class EisConfigurationClient
{

    public function latest(string $token): array
    {

        $response = Http::withToken($token)

            ->acceptJson()

            ->timeout(60)

            ->post(
                config('eis.base_url')
                . '/api/v1/configuration/get-latest-configs'
            );


        if(!$response->successful()){

            \Log::error(
                'EIS Configuration Client Error: '
                . $response->body()
            );

        }


        return $response->json();

    }

}