<?php

namespace Modules\EIS\Services\Stock;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Terminal\TerminalContext;

class StockOperationsClient
{
    public function adjustmentReasons(EisSetting $setting): array
    {
        $response = $this->request($setting)
            ->send('POST', 'stock/getStockAdjustmentReasons');
        $data = $response->json();
        if (!$response->successful() || !is_array($data) || (int) ($data['statusCode'] ?? 0) !== 1
            || !empty($data['errors'])) {
            throw new \UnexpectedValueException('EIS could not provide stock adjustment reasons.');
        }
        $reasons = $data['data'] ?? null;
        if (!is_array($reasons)) {
            throw new \UnexpectedValueException('EIS returned an invalid stock adjustment reason list.');
        }

        $descriptions = [];
        foreach ($reasons as $reason) {
            if (is_array($reason) && is_string($reason['description'] ?? null)
                && trim($reason['description']) !== '') {
                $descriptions[] = trim($reason['description']);
            }
        }

        return array_values(array_unique($descriptions));
    }

    public function submitAdjustment(EisSetting $setting, array $payload): array
    {
        return $this->result($this->request($setting)->post('stock/submit-adjustment', $payload));
    }

    public function submitTransfer(EisSetting $setting, array $payload): array
    {
        return $this->result($this->request($setting)->post('stock/transfer-inventory', $payload));
    }

    public function submitInformalPurchase(EisSetting $setting, array $payload): array
    {
        return $this->result($this->request($setting)->post('stock/submit-informal-purchase', $payload));
    }

    private function request(EisSetting $setting)
    {
        $base = rtrim(trim((string) config('eis.base_url')), '/');
        if ($base === '' || !is_string($setting->jwt_token) || trim($setting->jwt_token) === '') {
            throw new \InvalidArgumentException('EIS stock operations require a configured endpoint and terminal token.');
        }
        if (!preg_match('~/api/v1$~', $base)) {
            $base .= '/api/v1';
        }
        TerminalContext::assertBusiness($setting, (int) $setting->business_id);
        TerminalContext::site($setting);

        return Http::baseUrl($base.'/')
            ->withToken($setting->jwt_token)
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->connectTimeout(10);
    }

    private function result(Response $response): array
    {
        $body = $response->json();
        $safe = [];
        if (is_array($body)) {
            foreach (['statusCode', 'remark', 'errors'] as $key) {
                if (array_key_exists($key, $body)) {
                    $safe[$key] = $body[$key];
                }
            }
        }

        if ($response->serverError() || (!$response->successful() && !$response->clientError())
            || !is_array($body) || !array_key_exists('statusCode', $body)) {
            return ['outcome' => 'unknown', 'response' => $safe, 'message' => 'EIS did not return a conclusive stock operation result.'];
        }
        if ($response->clientError() || $body['statusCode'] === 0 || $body['statusCode'] === '0') {
            return ['outcome' => 'rejected', 'response' => $safe, 'message' => $this->remoteMessage($body)];
        }
        if (($body['statusCode'] !== 1 && $body['statusCode'] !== '1') || !empty($body['errors'])) {
            return [
                'outcome' => 'unknown',
                'response' => $safe,
                'message' => 'EIS returned mixed or incomplete stock operation results. Do not resubmit.',
            ];
        }

        return ['outcome' => 'submitted', 'response' => $safe, 'message' => null];
    }

    private function remoteMessage(array $body): string
    {
        $remark = $body['remark'] ?? null;
        if (is_string($remark) && trim($remark) !== '') {
            return mb_substr(trim($remark), 0, 1000);
        }

        return 'EIS rejected the stock operation.';
    }
}
