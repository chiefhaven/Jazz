<?php
namespace Modules\EIS\Services\Auth;

use Modules\EIS\Models\EisSetting;
use Illuminate\Support\Facades\Http;

class EisAuthService
{
    public function refresh(EisSetting $setting)
    {
        $response = Http::post($setting->base_url . '/auth/refresh', [
            'refresh_token' => $setting->refresh_token,
        ]);

        $data = $response->json();

        $setting->update([
            'jwt_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'token_expires_at' => now()->addMinutes(55),
        ]);

        return $setting;
    }
}