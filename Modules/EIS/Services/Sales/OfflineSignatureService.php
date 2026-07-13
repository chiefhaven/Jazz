<?php

namespace Modules\EIS\Services\Sales;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OfflineSignatureService
{
    /**
     * Generate offline data signature and validation URL.
     *
     * @param string $taxpayerId
     * @param int $position
     * @param array $request
     * @param string $secretKey
     * @return array
     */
    public function generateInvoiceResponse(
        string $taxpayerId,
        int $position,
        array $request,
        string $secretKey
    ): array {
        try {
            // Get julian date from transaction date
            $transactionDate = $request['transactiondate'] ?? now()->toISOString();
            $julianDate = $this->toJulianDate($transactionDate);
            
            // Convert julian date to Base64
            $julianDateTo64 = $this->base10ToBase64((string)$julianDate);
            
            // Generate combined string
            $combinedString = $this->generateCombinedString(
                $taxpayerId,
                $position,
                $julianDate,
                $request['transactionCount'] ?? 1
            );
            
            // Build parameters
            $param = "TI=" . $combinedString . 
                     "&N=" . ($request['NumItems'] ?? 0) . 
                     "&I=" . ($request['InvoiceTotal'] ?? 0) . 
                     "&V=" . ($request['VATAmount'] ?? 0) . 
                     "&T=" . $julianDateTo64;
            
            // Compute HMAC-SHA256 signature
            $offlineDataSignature = $this->computeHMACSHA256($param, $secretKey);
            
            // URL encode the signature
            $offlineDataSignature = urlencode($offlineDataSignature);
            
            // Build validation URL
            $offlineBaseURL = "https://dev-eis-portal.mra.mw/ReceiptValidation/Validate/";
            $validationURL = $offlineBaseURL . "?" . $param . "&S=" . $offlineDataSignature;
            
            Log::debug('Offline signature generated', [
                'julian_date' => $julianDate,
                'julian_date_64' => $julianDateTo64,
                'combined_string' => $combinedString,
                'param' => $param,
                'signature' => $offlineDataSignature,
                'validation_url' => $validationURL
            ]);
            
            return [
                'offlineDataSignature' => $offlineDataSignature,
                'validationURL' => $validationURL,
                'param' => $param,
                'julianDate' => $julianDate,
                'julianDateTo64' => $julianDateTo64,
                'combinedString' => $combinedString,
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to generate offline signature', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Failed to generate offline signature: ' . $e->getMessage());
        }
    }

    /**
     * Convert date to Julian date format (YYDDD).
     *
     * @param string $dateString
     * @return int
     */
    public function toJulianDate(string $dateString): int
    {
        $date = new \DateTime($dateString);
        $year = (int) $date->format('y');
        $dayOfYear = (int) $date->format('z') + 1;
        
        // Combine year and day of year
        return (int) ($year . str_pad($dayOfYear, 3, '0', STR_PAD_LEFT));
    }

    /**
     * Convert base10 number to base64.
     *
     * @param string $number
     * @return string
     */
    public function base10ToBase64(string $number): string
    {
        // Convert to integer
        $num = (int) $number;
        
        // If number is 0, return base64 of 0
        if ($num === 0) {
            return 'MA=='; // Base64 of '0'
        }
        
        // Convert to base64
        return $this->customBase64Encode((string)$num);
    }

    /**
     * Custom base64 encoding for numbers.
     *
     * @param string $number
     * @return string
     */
    private function customBase64Encode(string $number): string
    {
        return rtrim(strtr(base64_encode($number), '+/', '-_'), '=');
    }

    /**
     * Generate combined string from components.
     *
     * @param string $taxpayerId
     * @param int $position
     * @param int $julianDate
     * @param int $transactionCount
     * @return string
     */
    public function generateCombinedString(
        string $taxpayerId,
        int $position,
        int $julianDate,
        int $transactionCount
    ): string {
        // Format: TAXPAYERID-POSITION-JULIANDATE-COUNT
        return $taxpayerId . '-' . $position . '-' . $julianDate . '-' . $transactionCount;
    }

    /**
     * Compute HMAC-SHA256 signature.
     *
     * @param string $param
     * @param string $secretKey
     * @return string
     */
    public function computeHMACSHA256(string $param, string $secretKey): string
    {
        // Compute HMAC-SHA256 hash
        $hash = hash_hmac('sha256', $param, $secretKey, true);
        return base64_encode($hash);
    }

    /**
     * Generate offline signature with URL encoding.
     *
     * @param string $param
     * @param string $secretKey
     * @return string
     */
    public function generateOfflineSignature(string $param, string $secretKey): string
    {
        $signature = $this->computeHMACSHA256($param, $secretKey);
        return urlencode($signature);
    }

    /**
     * Validate an offline signature.
     *
     * @param string $param
     * @param string $signature
     * @param string $secretKey
     * @return bool
     */
    public function validateOfflineSignature(string $param, string $signature, string $secretKey): bool
    {
        $expectedSignature = $this->generateOfflineSignature($param, $secretKey);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate full validation URL.
     *
     * @param string $taxpayerId
     * @param int $position
     * @param array $request
     * @param string $secretKey
     * @param string $baseURL
     * @return string
     */
    public function generateValidationURL(
        string $taxpayerId,
        int $position,
        array $request,
        string $secretKey,
        string $baseURL = 'https://dev-eis-portal.mra.mw/ReceiptValidation/Validate/'
    ): string {
        $result = $this->generateInvoiceResponse($taxpayerId, $position, $request, $secretKey);
        return $result['validationURL'];
    }

    /**
     * Generate receipt validation QR code data.
     *
     * @param string $validationURL
     * @return string
     */
    public function generateQRCodeData(string $validationURL): string
    {
        // Base64 encode the validation URL for QR code
        return base64_encode($validationURL);
    }

    /**
     * Parse invoice components from combined string.
     *
     * @param string $combinedString
     * @return array|null
     */
    public function parseCombinedString(string $combinedString): ?array
    {
        $parts = explode('-', $combinedString);
        
        if (count($parts) !== 4) {
            return null;
        }
        
        return [
            'taxpayerId' => $parts[0],
            'position' => (int) $parts[1],
            'julianDate' => (int) $parts[2],
            'transactionCount' => (int) $parts[3],
        ];
    }
}