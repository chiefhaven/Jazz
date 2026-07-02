<?php

namespace App\EIS\Services\Onboarding;

use App\EIS\Services\Http\HttpClientService;
use App\EIS\Models\EisTerminal;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\EIS\Exceptions\EisException;

class ActivationService
{
    public function __construct(
        protected HttpClientService $http
    ) {}

    /**
     * Step 1: Request activation code
     */
    public function requestActivation(array $deviceInfo): array
    {
        $terminalId = config('eis.terminal_id');

        $payload = [
            'terminalId' => $terminalId,
            'deviceFingerprint' => $this->generateFingerprint($deviceInfo),
            'productId' => $deviceInfo['product_id'] ?? null,
            'productVersion' => $deviceInfo['product_version'] ?? null,
            'os' => $deviceInfo['os'] ?? php_uname(),
            'macAddress' => $deviceInfo['mac_address'] ?? null,
        ];

        $response = $this->http->post('onboarding/activate', $payload);

        if (!isset($response['data']['activationCode'])) {
            throw new EisException("Activation failed: invalid response");
        }

        EisTerminal::updateOrCreate(
            ['terminal_id' => $terminalId],
            [
                'activation_code' => $response['data']['activationCode'],
                'status' => 'pending',
                'device_fingerprint' => $payload['deviceFingerprint'],
                'product_id' => $payload['productId'],
                'product_version' => $payload['productVersion'],
                'os' => $payload['os'],
                'mac_address' => $payload['macAddress'],
            ]
        );

        return $response['data'];
    }

    /**
     * Step 2: Confirm activation
     */
    public function confirmActivation(string $activationCode): array
    {
        $terminal = EisTerminal::where('activation_code', $activationCode)->first();

        if (!$terminal) {
            throw new EisException("Invalid activation code");
        }

        $response = $this->http->post('onboarding/confirm', [
            'terminalId' => $terminal->terminal_id,
            'activationCode' => $activationCode,
        ]);

        $terminal->update([
            'status' => 'active',
            'activated_at' => Carbon::now(),
        ]);

        return $response['data'] ?? [];
    }

    /**
     * Device fingerprint generator
     */
    private function generateFingerprint(array $deviceInfo): string
    {
        return hash('sha256', json_encode([
            $deviceInfo['product_id'] ?? '',
            $deviceInfo['mac_address'] ?? '',
            php_uname(),
            config('eis.terminal_id'),
        ]));
    }

    /**
     * Check activation status
     */
    public function status(): ?EisTerminal
    {
        return EisTerminal::where('terminal_id', config('eis.terminal_id'))->first();
    }
}