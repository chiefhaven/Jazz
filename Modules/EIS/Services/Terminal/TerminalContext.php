<?php

namespace Modules\EIS\Services\Terminal;

use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Products\OpeningStockInitializer;

final class TerminalContext
{
    public static function site(object $settings): string
    {
        $value = $settings->site_id ?? $settings->branch_id ?? null;
        $site = OpeningStockInitializer::siteId(['siteId' => $value]);
        if ($site === null || $site !== (string) $value) {
            throw new \InvalidArgumentException('EIS terminal requires an exact configured site ID.');
        }
        return $site;
    }

    public static function assertBusiness(object $settings, int $businessId): void
    {
        if ($businessId <= 0 || (string) ($settings->business_id ?? '') !== (string) $businessId) {
            throw new \InvalidArgumentException('EIS settings do not belong to this business terminal.');
        }
    }

    public static function settings(int $businessId, ?string $token = null): EisSetting
    {
        $rows = EisSetting::where('business_id', $businessId)->limit(2)->get();
        if ($rows->count() !== 1) {
            throw new \InvalidArgumentException('EIS business requires exactly one terminal settings record.');
        }
        $settings = $rows->first();
        if ($token !== null && (!is_string($settings->jwt_token) || !hash_equals($settings->jwt_token, $token))) {
            throw new \InvalidArgumentException('EIS credentials do not belong to this business terminal.');
        }
        return $settings;
    }

    public static function assertConfiguration(object $settings, object $data): void
    {
        $site = $data->terminalConfiguration->terminalSite->siteId ?? null;
        $tin = $data->taxpayerConfiguration->tin ?? null;
        if ((!is_string($site) && !is_int($site)) || (string) $site !== self::site($settings)
            || (!is_string($tin) && !is_int($tin)) || (string) $tin !== (string) $settings->tpin) {
            throw new \InvalidArgumentException('EIS configuration does not match the business terminal site and TIN.');
        }
        $terminalId = $data->terminalConfiguration->terminalId ?? null;
        if ($terminalId !== null && (string) $terminalId !== (string) $settings->device_id) {
            throw new \InvalidArgumentException('EIS configuration belongs to a different terminal.');
        }
    }
}
