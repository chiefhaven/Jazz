<?php

namespace Modules\EIS\Services\Configuration;

use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Models\EisTaxRate;

class TaxConfigurationService
{


    public function sync(
        EisConfiguration $configuration,
        array $data
    ){

        foreach(
            $data['globalConfiguration']['taxrates'] ?? []
            as $tax
        ){

            EisTaxRate::updateOrCreate(

                [
                    'configuration_id'=>$configuration->id,
                    'eis_tax_rate_id'=>$tax['id']
                ],

                [
                    'name'=>$tax['name'],
                    'charge_mode'=>$tax['chargeMode'],
                    'ordinal'=>$tax['ordinal'],
                    'rate'=>$tax['rate']

                ]

            );

        }

    }

}