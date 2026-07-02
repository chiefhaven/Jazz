<?php

namespace App\EIS\Support;

class Eis
{
    public static function baseUrl(): string
    {
        return config('eis.base_url');
    }

    public static function terminalId(): ?string
    {
        return config('eis.terminal_id');
    }
}