<?php

namespace Modules\EIS\Services\Configuration;


use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Models\EisTaxRate;


class ConfigurationSyncService
{

    public function __construct(
        protected EisConfiguration $client
    ){}



    public function sync(
        int $businessId,
        string $token
    ) {

        $response = $this->client->latest($token);


        if (($response['statusCode'] ?? 1) !== 0) {

            throw new \Exception(
                $response['remark']
            );

        }


        $data = $response['data'];



        $configuration = EisConfiguration::updateOrCreate(

            [
                'business_id'=>$businessId
            ],

            [

                'global_version'
                    =>
                    $data['globalConfiguration']['versionNo'] ?? null,


                'terminal_version'
                    =>
                    $data['terminalConfiguration']['versionNo'] ?? null,


                'taxpayer_version'
                    =>
                    $data['taxpayerConfiguration']['versionNo'] ?? null,


                'tin'
                    =>
                    $data['taxpayerConfiguration']['tin'] ?? null,


                'is_vat_registered'
                    =>
                    $data['taxpayerConfiguration']['isVATRegistered'] ?? false,


                'tax_office_code'
                    =>
                    $data['taxpayerConfiguration']['taxOfficeCode'] ?? null,


                'raw_response'
                    =>
                    $data,


                'last_synced_at'
                    =>
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




    private function syncTaxRates(
        $configuration,
        $config
    ){

        $rates =
            $config['globalConfiguration']['taxrates']
            ?? [];


        foreach($rates as $rate){


            EisTaxRate::updateOrCreate(
                [

                    'configuration_id'
                    =>
                    $configuration->id,


                    'eis_tax_rate_id'
                    =>
                    $rate['id']

                ],

                [

                    'name'
                    =>
                    $rate['name'],


                    'charge_mode'
                    =>
                    $rate['chargeMode'],


                    'rate'
                    =>
                    $rate['rate'],


                    'ordinal'
                    =>
                    $rate['ordinal']

                ]
            );

        }

    }

}