<?php

namespace Modules\EIS\Services\Invoice;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisInvoiceSequence;
use Modules\EIS\Models\EisSale;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Models\EisTerminalConfiguration;

class InvoiceNumberGenerator
{
    /**
     * Generate EIS compliant invoice number.
     * Format: Base64(TaxpayerID) - Base64(TerminalPosition) - Base64(JulianDate) - Base64(Count)
     *
     * @param int $businessId
     * @param int|null $terminalPosition
     * @return string
     */
    public function generateInvoiceNumber(int $businessId, ?int $terminalPosition = null): string
    {
        try {
            Log::info('Generating EIS invoice number', [
                'business_id' => $businessId,
                'terminal_position' => $terminalPosition
            ]);

            return DB::transaction(function () use ($businessId, $terminalPosition) {
                // Get EIS settings
                $setting = EisSetting::where('business_id', $businessId)->first();
                if (!$setting) {
                    throw new \Exception('EIS settings not found for business: ' . $businessId);
                }

                // Get terminal configuration for position
                $terminal = null;
                if ($terminalPosition) {
                    $terminal = EisTerminalConfiguration::where('configuration_id', $businessId)
                        ->where('terminal_position', $terminalPosition)
                        ->first();
                } else {
                    $terminal = EisTerminalConfiguration::where('configuration_id', $businessId)
                        ->orderBy('id')
                        ->first();
                }

                if (!$terminal) {
                    throw new \Exception('Terminal configuration not found for business: ' . $businessId);
                }

                $count =EisSale::where('business_id', $businessId)
                    ->whereDate('created_at', now()->toDateString())
                    ->count() + 1;

                // Get components
                $taxpayerId = $setting->tpin ?? null;
                $terminalPos = $terminal->terminal_position ?? $terminalPosition ?? 1;
                $julianDate = $this->getJulianDate(now());
                
                // Encode each component to Base64
                $encodedTaxpayerId = $this->base64Encode($taxpayerId);
                $encodedTerminalPos = $this->base64Encode((string)$terminalPos);
                $encodedJulianDate = $this->base64Encode($julianDate);
                $encodedCount = $this->base64Encode((string)$count);

                // Build invoice number
                $invoiceNumber = $encodedTaxpayerId . '-' . 
                                $encodedTerminalPos . '-' . 
                                $encodedJulianDate . '-' . 
                                $encodedCount;

                Log::info('Invoice number generated successfully', [
                    'business_id' => $businessId,
                    'invoice_number' => $invoiceNumber,
                    'components' => [
                        'taxpayer_id' => $taxpayerId,
                        'terminal_position' => $terminalPos,
                        'julian_date' => $julianDate,
                        'count' => $count,
                        'encoded_taxpayer_id' => $encodedTaxpayerId,
                        'encoded_terminal_pos' => $encodedTerminalPos,
                        'encoded_julian_date' => $encodedJulianDate,
                        'encoded_count' => $encodedCount,
                    ]
                ]);

                return $invoiceNumber;
            });

        } catch (\Exception $e) {
            Log::error('Failed to generate invoice number', [
                'business_id' => $businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Failed to generate invoice number: ' . $e->getMessage());
        }
    }

    /**
     * Generate multiple invoice numbers.
     *
     * @param int $businessId
     * @param int $count
     * @param int|null $terminalPosition
     * @return array
     */
    public function generateInvoiceNumbers(int $businessId, int $count, ?int $terminalPosition = null): array
    {
        try {
            Log::info('Generating multiple invoice numbers', [
                'business_id' => $businessId,
                'count' => $count,
                'terminal_position' => $terminalPosition
            ]);

            $invoiceNumbers = [];
            
            for ($i = 0; $i < $count; $i++) {
                $invoiceNumbers[] = $this->generateInvoiceNumber($businessId, $terminalPosition);
            }
            
            return $invoiceNumbers;

        } catch (\Exception $e) {
            Log::error('Failed to generate multiple invoice numbers', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get Julian date.
     * Format: YYDDD (Year + Day of year)
     *
     * @param \DateTime $date
     * @return string
     */
    private function getJulianDate(\DateTime $date): string
    {
        // Get last two digits of year
        $year = $date->format('y');
        // Get day of year (001-366)
        $dayOfYear = str_pad($date->format('z') + 1, 3, '0', STR_PAD_LEFT);
        
        return $year . $dayOfYear;
    }

    /**
     * Base64 encode a string.
     *
     * @param string $value
     * @return string
     */
    private function base64Encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    /**
     * Decode a Base64 encoded string.
     *
     * @param string $encoded
     * @return string
     */
    private function base64Decode(string $encoded): string
    {
        return base64_decode(strtr($encoded, '-_', '+/'));
    }

    /**
     * Parse invoice number to get components.
     *
     * @param string $invoiceNumber
     * @return array|null
     */
    public function parseInvoiceNumber(string $invoiceNumber): ?array
    {
        try {
            $parts = explode('-', $invoiceNumber);
            
            if (count($parts) !== 4) {
                return null;
            }

            return [
                'taxpayer_id' => $this->base64Decode($parts[0]),
                'terminal_position' => $this->base64Decode($parts[1]),
                'julian_date' => $this->base64Decode($parts[2]),
                'count' => (int) $this->base64Decode($parts[3]),
                'encoded_taxpayer_id' => $parts[0],
                'encoded_terminal_position' => $parts[1],
                'encoded_julian_date' => $parts[2],
                'encoded_count' => $parts[3],
            ];

        } catch (\Exception $e) {
            Log::error('Failed to parse invoice number', [
                'invoice_number' => $invoiceNumber,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Validate invoice number format.
     *
     * @param string $invoiceNumber
     * @return bool
     */
    public function validateInvoiceNumber(string $invoiceNumber): bool
    {
        $parsed = $this->parseInvoiceNumber($invoiceNumber);
        
        if (!$parsed) {
            return false;
        }

        // Validate count is numeric
        if (!is_numeric($parsed['count'])) {
            return false;
        }

        // Validate components are not empty
        if (empty($parsed['taxpayer_id']) || empty($parsed['terminal_position'])) {
            return false;
        }

        return true;
    }
}