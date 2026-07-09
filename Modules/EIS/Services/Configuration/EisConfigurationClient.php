<?php

namespace Modules\EIS\Services\Configuration;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EisConfigurationClient
{

    public function latest(string $token): object
    {

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(60)
            ->post(
                config('eis.base_url')
                . '/api/v1/configuration/get-latest-configs'
            );


        if (!$response->successful()) {

            Log::error(
                'EIS Configuration Client Error',
                [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]
            );

            throw new \Exception(
                'EIS Configuration API request failed'
            );
        }


        return $response->object();

    }

}
:::