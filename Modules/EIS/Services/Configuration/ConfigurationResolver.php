<?php

namespace Modules\EIS\Services\Configuration;

use Modules\EIS\Models\EisConfiguration;

class ConfigurationResolver
{

    public function get(int $businessId)
    {
        return EisConfiguration::where(
            'business_id',
            $businessId
        )
        ->latest()
        ->first();
    }


    public function sellerTin(int $businessId)
    {
        return $this->get($businessId)
            ?->tpin;
    }


    public function siteId(int $businessId)
    {
        return \Modules\EIS\Services\Terminal\TerminalContext::site(
            \Modules\EIS\Services\Terminal\TerminalContext::settings($businessId)
        );
    }


    public function vatRegistered(int $businessId)
    {
        return $this->get($businessId)
            ?->is_vat_registered;
    }


    public function taxRates(int $businessId)
    {
        return $this->get($businessId)
            ?->taxRates;
    }

}