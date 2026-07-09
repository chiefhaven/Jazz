<?php

namespace Modules\EIS\Services\Configuration;

use Modules\EIS\Models\EisConfiguration;

class ConfigurationSyncService
{
    public function __construct(
        protected EisConfigurationClient $client
    ) {
    }


    public function sync(
        int $businessId,
        string $token
    ) {

        $response = $this->client->latest($token);


        if (!is_object($response)) {

            throw new \Exception(
                'Invalid EIS response format: ' . gettype($response)
            );

        }


        if (($response->statusCode ?? 1) !== 0) {

            throw new \Exception(
                $response->remark ?? 'EIS configuration sync failed'
            );

        }


        $data = $response->data;


        $configuration = EisConfiguration::updateOrCreate(
            [
                'business_id' => $businessId
            ],
            [
                'global_version' =>
                    $data->globalConfiguration->versionNo ?? null,

                'terminal_version' =>
                    $data->terminalConfiguration->versionNo ?? null,

                'taxpayer_version' =>
                    $data->taxpayerConfiguration->versionNo ?? null,

                'tin' =>
                    $data->taxpayerConfiguration->tin ?? null,

                'is_vat_registered' =>
                    $data->taxpayerConfiguration->isVATRegistered ?? false,

                'tax_office_code' =>
                    $data->taxpayerConfiguration->taxOfficeCode ?? null,

                'raw_response' =>
                    json_encode($response),

                'last_synced_at' =>
                    now()
            ]
        );


        app(TaxConfigurationService::class)
            ->sync(
                $configuration,
                $data
            );


        app(TerminalConfigurationService::class)
            ->sync(
                $configuration,
                $data
            );


        return $configuration;

    }

}