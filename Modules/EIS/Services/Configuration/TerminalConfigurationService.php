<?php

namespace Modules\EIS\Services\Configuration;

use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Models\EisTerminalConfiguration;


class TerminalConfigurationService
{


    public function sync(EisConfiguration $configuration,  array $data){

        \Modules\EIS\Services\Terminal\TerminalContext::assertConfiguration(
            \Modules\EIS\Services\Terminal\TerminalContext::settings((int) $configuration->business_id),
            json_decode(json_encode($data, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR)
        );

        $terminal = $data['terminalConfiguration'];


        EisTerminalConfiguration::updateOrCreate(

            ['configuration_id'=>$configuration->id],

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

                'max_cumulative_amount' => $terminal['offlineLimit']['maxCummulativeAmount'],

                'address_lines' => $terminal['addressLines'],
            ]

        );

        $business = \App\Business::find($configuration->business_id);

        $business->update([
            "name" => $terminal['tradingName'],
            "tax_number_1" => $terminal['tin'] ?? null,
        ]);
        


    }


}