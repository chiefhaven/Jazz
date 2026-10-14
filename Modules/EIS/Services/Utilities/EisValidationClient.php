<?php

namespace Modules\EIS\Services\Utilities;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Modules\EIS\Models\EisSetting;

/** Contracts: https://eis-api.mra.mw/swagger/v1/swagger.json */
class EisValidationClient
{
    public function checkTinAuthorizationRequirement(EisSetting $settings, string $tin): array
    {
        return $this->post($settings, 'check-tin-authorization-requirement', ['tin' => $tin], 'buyerTIN');
    }

    public function validateAuthorizationCode(EisSetting $settings, string $code): array
    {
        $data = $this->post($settings, 'validate-authorization-code', ['authorizationCode' => $code], 'buyerAuthorizationCode');
        if (($data['isValidAuthorizationCode'] ?? null) !== true || ($data['authorizationCode'] ?? null) !== $code) {
            throw ValidationException::withMessages(['buyerAuthorizationCode' => 'EIS rejected the buyer authorization code.']);
        }
        return $data;
    }

    public function validateVat5Certificate(EisSetting $settings, array $certificate): array
    {
        $data = $this->post($settings, 'validate-vat5-certificate', $certificate, 'vat5_certificate_number');
        if (($data['isValid'] ?? null) !== true
            || ($data['projectNumber'] ?? null) !== $certificate['projectNumber']
            || ($data['vat5CertificateNumber'] ?? null) !== $certificate['certificateNumber']
            || ! is_numeric($data['quantity'] ?? null) || (float) $data['quantity'] !== $certificate['quantity']) {
            throw ValidationException::withMessages(['vat5_certificate_number' => 'EIS rejected the VAT5 certificate.']);
        }
        return $data;
    }

    private function post(EisSetting $settings, string $endpoint, array $payload, string $field): array
    {
        $base = rtrim((string) config('eis.base_url'), '/');
        // Existing deployments use either the host or a base ending in /api/v1.
        $base = preg_replace('~/api/v1$~', '', $base);
        if ($base === '' || trim((string) $settings->jwt_token) === '') {
            throw ValidationException::withMessages([$field => 'EIS validation is not configured for this business.']);
        }
        try {
            $response = Http::acceptJson()->asJson()->withToken($settings->jwt_token)
                ->connectTimeout(10)->timeout((int) config('eis.timeout', 30))
                ->post($base.'/api/v1/utilities/'.$endpoint, $payload);
        } catch (ConnectionException $e) {
            throw ValidationException::withMessages([$field => 'EIS validation is unavailable. Please retry before saving the sale.']);
        }
        $body = $response->json();
        if (! $response->successful() || ! is_array($body) || ($body['statusCode'] ?? null) !== 1
            || ! empty($body['errors']) || ! is_array($body['data'] ?? null)) {
            throw ValidationException::withMessages([$field => 'EIS could not validate the buyer or certificate. Check the details and retry.']);
        }
        return $body['data'];
    }
}