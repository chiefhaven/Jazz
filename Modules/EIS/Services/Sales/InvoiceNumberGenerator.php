<?php

namespace Modules\EIS\Services\Sales;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Models\EisSale;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Models\EisTerminalConfiguration;

class InvoiceNumberGenerator
{
    /**
     * Base64 character set for encoding.
     */
    protected const BASE64_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    /**
     * Convert base10 number to Base64.
     * 
     * @param int|string $number
     * @return string
     */
    public function base10ToBase64($number): string
    {
        $number = (int) $number;
        
        if ($number == 0) {
            return 'A';
        }
        
        $result = '';
        $base64Chars = self::BASE64_CHARS;
        
        while ($number > 0) {
            $remainder = $number % 64;
            $result = $base64Chars[$remainder] . $result;
            $number = (int) ($number / 64);
        }
        
        return $result;
    }

    /**
     * Convert Base64 back to base10 number.
     * 
     * @param string $base64
     * @return int
     */
    public function base64ToBase10(string $base64): int
    {
        $base64Chars = self::BASE64_CHARS;
        $result = 0;
        $length = strlen($base64);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $base64[$i];
            $value = strpos($base64Chars, $char);
            if ($value === false) {
                return 0;
            }
            $result = ($result * 64) + $value;
        }
        
        return $result;
    }

    /**
     * Convert date to astronomical Julian Date (JD).
     * This matches the C# implementation exactly.
     * 
     * @param \DateTime $date
     * @return int
     */
    public function toJulianDate(\DateTime $date): int
    {
        $date = clone $date;
        $date->setTime(0, 0, 0);
        
        $year = (int) $date->format('Y');
        $month = (int) $date->format('m');
        $day = (int) $date->format('d');
        
        // Adjust for Julian calendar
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }
        
        $A = (int) ($year / 100);
        $B = 2 - $A + (int) ($A / 4);
        
        // Calculate Julian Date
        $JD = (int) (floor(365.25 * ($year + 4716)) 
            + floor(30.6001 * ($month + 1)) 
            + $day + $B - 1524);
        
        return $JD;
    }

    /**
     * Generate EIS compliant invoice number.
     * Format: Base64(TaxpayerID) - Base64(TerminalPosition) - Base64(JulianDate) - Base64(Count)
     *
     * @param int $businessId
     * @param int|null $terminalPosition
     * @return string
     */
    public function generateInvoiceNumber(int $businessId, int $transactionId, ?int $terminalPosition, ?int $taxpayerId): string
    {
        try {
            Log::info('Generating EIS invoice number', [
                'business_id' => $businessId,
                'terminal_position' => $terminalPosition
            ]);

            return DB::transaction(function () use ($businessId, $transactionId, $terminalPosition, $taxpayerId) {
                // Get EIS settings
                $setting = EisSetting::where('business_id', $businessId)->first();
                if (!$setting) {
                    throw new \Exception('EIS settings not found for business: ' . $businessId);
                }

                // Get configuration with terminal relationship
                $configuration = EisConfiguration::with('terminalConfiguration')
                    ->where('business_id', $businessId)
                    ->first();
                    
                if (!$configuration) {
                    throw new \Exception('EIS configuration not found for business: ' . $businessId);
                }

                // Get terminal position
                $terminalPos = $terminalPosition ?? 1;

                // Get count for today
                $identifier = $businessId.$transactionId;

                    Log::info('Configuratons: ',[$configuration]);
                
                // Use full astronomical Julian Date
                $julianDate = $this->toJulianDate(now());
                
                // Encode each component to Base64
                $encodedTaxpayerId = $this->base10ToBase64($taxpayerId);
                $encodedTerminalPos = $this->base10ToBase64($terminalPos);
                $encodedJulianDate = $this->base10ToBase64($julianDate);
                $encodedCount = $this->base10ToBase64($identifier);

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

            return $this->generateFallbackInvoiceNumber($businessId);
        }
    }

    /**
     * Create default terminal configuration.
     *
     * @param EisConfiguration $configuration
     * @param int $terminalPosition
     * @return EisTerminalConfiguration
     */
    protected function createDefaultTerminal(EisConfiguration $configuration, int $terminalPosition): EisTerminalConfiguration
    {
        try {
            $terminal = EisTerminalConfiguration::create([
                'configuration_id' => $configuration->id,
                'terminal_position' => $terminalPosition,
                'terminal_label' => 'Default Terminal',
                'is_active' => true,
                'is_confirmed' => true,
                'version' => 1,
                'last_synced_at' => now(),
            ]);

            Log::info('Default terminal created', [
                'configuration_id' => $configuration->id,
                'terminal_position' => $terminalPosition,
                'terminal_id' => $terminal->id
            ]);

            return $terminal;

        } catch (\Exception $e) {
            Log::error('Failed to create default terminal', [
                'configuration_id' => $configuration->id,
                'error' => $e->getMessage()
            ]);

            return new EisTerminalConfiguration([
                'configuration_id' => $configuration->id,
                'terminal_position' => $terminalPosition,
                'terminal_label' => 'Default Terminal',
                'is_active' => true,
            ]);
        }
    }

    /**
     * Generate fallback invoice number.
     *
     * @param int $businessId
     * @return string
     */
    private function generateFallbackInvoiceNumber(int $businessId): string
    {
        $setting = EisSetting::where('business_id', $businessId)->first();
        $tpin = $setting->tpin ?? '000000';
        $date = now()->format('Ymd');
        $timestamp = now()->format('His');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return 'FALLBACK-' . $tpin . '-' . $date . '-' . $timestamp . '-' . $random;
    }

    /**
     * Get terminal position from terminal configuration.
     *
     * @param int $businessId
     * @param int|null $terminalPosition
     * @return int
     */
    public function getTerminalPosition(int $businessId, ?int $terminalPosition = null): int
    {
        try {
            $configuration = EisConfiguration::with('terminalConfiguration')
                ->where('business_id', $businessId)
                ->first();
                
            if (!$configuration) {
                return $terminalPosition ?? 1;
            }

            $terminal = $configuration->terminalConfiguration;

            if ($terminal) {
                return $terminal->terminal_position ?? $terminalPosition ?? 1;
            }

            $setting = EisSetting::where('business_id', $businessId)->first();
            if ($setting && $setting->terminal_position) {
                return $setting->terminal_position;
            }

            return $terminalPosition ?? 1;

        } catch (\Exception $e) {
            Log::warning('Failed to get terminal position, using default', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);
            return $terminalPosition ?? 1;
        }
    }

    /**
     * Get or create terminal configuration.
     *
     * @param int $businessId
     * @param int|null $terminalPosition
     * @return EisTerminalConfiguration
     */
    public function getOrCreateTerminal(int $businessId, ?int $terminalPosition = null): EisTerminalConfiguration
    {
        try {
            $configuration = EisConfiguration::with('terminalConfiguration')
                ->where('business_id', $businessId)
                ->first();

            if (!$configuration) {
                throw new \Exception('Configuration not found for business: ' . $businessId);
            }

            $terminal = $configuration->terminalConfiguration;

            if (!$terminal) {
                $terminal = $this->createDefaultTerminal(
                    $configuration,
                    $terminalPosition ?? 1
                );
            }

            return $terminal;

        } catch (\Exception $e) {
            Log::error('Failed to get or create terminal', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return new EisTerminalConfiguration([
                'configuration_id' => $businessId,
                'terminal_position' => $terminalPosition ?? 1,
                'terminal_label' => 'Default Terminal',
                'is_active' => true,
            ]);
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
     * Get daily transaction count.
     *
     * @param int $businessId
     * @return int
     */
    public function getDailyCount(int $businessId): int
    {
        return EisSale::where('business_id', $businessId)
            ->whereDate('created_at', now()->toDateString())
            ->count();
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
                'taxpayer_id' => $this->base64ToBase10($parts[0]),
                'terminal_position' => (int) $this->base64ToBase10($parts[1]),
                'julian_date' => (int) $this->base64ToBase10($parts[2]),
                'count' => (int) $this->base64ToBase10($parts[3]),
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

        if (!is_numeric($parsed['count'])) {
            return false;
        }

        if (empty($parsed['taxpayer_id']) || empty($parsed['terminal_position'])) {
            return false;
        }

        return true;
    }
}