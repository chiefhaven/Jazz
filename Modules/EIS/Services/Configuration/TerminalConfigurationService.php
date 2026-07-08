<?php

namespace Modules\EIS\Services\Configuration;

use Modules\EIS\Models\EisTerminalConfiguration;


class TerminalConfigurationService
{


    public function sync(
    EisConfiguration $configuration,
    array $data
    ){

        $terminal =
            $data['terminalConfiguration'];


        EisTerminalConfiguration::updateOrCreate(

            [
                'configuration_id'=>$configuration->id
            ],

            [

                'version_no'=>$terminal['versionNo'],

                'terminal_label'=>$terminal['terminalLabel'],

                'is_active_terminal'=>$terminal['isActiveTerminal'],

                'email_address'=>$terminal['emailAddress'],

                'phone_number'=>$terminal['phoneNumber'],

                'trading_name'=>$terminal['tradingName'],

                'site_id'=>$terminal['terminalSite']['siteId'],

                'site_name'=>$terminal['terminalSite']['siteName'],

                'max_transaction_age_hours'=>
                    $terminal['offlineLimit']['maxTransactionAgeInHours'],

                'max_cumulative_amount'=>
                    $terminal['offlineLimit']['maxCummulativeAmount'],

                'address_lines'=>
                    $terminal['addressLines'],

            ]

        );

    }


}