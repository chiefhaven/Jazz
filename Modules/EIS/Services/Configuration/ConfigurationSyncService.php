<?php

namespace Modules\EIS\Services\Configuration;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Exceptions\SyncException;
use Modules\EIS\Models\EisOfflineLimit;
use Modules\EIS\Models\EisTaxRate;
use Modules\EIS\Models\EisTerminalConfiguration;
use Modules\EIS\Models\EisTerminalSite;
use Modules\EIS\Services\Configuration\EISConfigurationResponse;
use Modules\EIS\Services\Configuration\Validators\ConfigurationValidator;

class ConfigurationSyncService
{
    private const REQUIRED_CONFIG_FIELDS = [
        'globalConfiguration',
        'terminalConfiguration',
        'taxpayerConfiguration'
    ];

    private const NON_RETRYABLE_ERRORS = [
        'invalid token',
        'authentication failed',
        'business not found',
        'validation error',
        'invalid configuration',
        'tpin not found',
        'taxpayer not found',
        'business not active',
        'account suspended',
        'invalid business id',
        'configuration validation failed',
        'permission denied',
        'access denied',
        'unauthorized'
    ];

    public function __construct(
        protected EisConfigurationClient $client,
        protected ConfigurationValidator $validator
    ) {
    }

    /**
     * Synchronize EIS configuration for a business.
     *
     * @param int $businessId
     * @param string $token
     * @return EisConfiguration
     * @throws SyncException
     * @throws \InvalidArgumentException
     */
    public function sync(
        int $businessId,
        string $token
    ): EisConfiguration {
        $this->validateBusinessId($businessId);
        $this->validateToken($token);

        Log::info('Configuration sync started', [
            'business_id' => $businessId
        ]);

        try {
            return DB::transaction(function () use ($businessId, $token) {
                // Fetch configuration from EIS
                $response = $this->client->latest($token);

                // Log response summary
                Log::debug('EIS API response received', [
                    'business_id' => $businessId,
                    'status_code' => $response->getStatusCode(),
                    'remark' => $response->getRemark(),
                    'is_success' => $response->isSuccess(),
                    'has_data' => $response->hasCompleteData(),
                    'has_errors' => $response->hasErrors()
                ]);

                // Validate response status
                $this->validateResponseStatus($response);

                // Get and validate data
                $data = $response->getData();
                $this->validateResponseData($data);

                // Check if data has changed before syncing
                $existingConfig = EisConfiguration::where('business_id', $businessId)->first();
                if ($existingConfig && !$this->hasConfigurationChanged($existingConfig, $data)) {
                    Log::info('Configuration unchanged, skipping sync', [
                        'business_id' => $businessId,
                        'global_version' => $existingConfig->global_version,
                        'terminal_version' => $existingConfig->terminal_version,
                        'taxpayer_version' => $existingConfig->taxpayer_version
                    ]);
                    
                    // Update last_synced_at even if unchanged
                    $existingConfig->update(['last_synced_at' => now()]);
                    return $existingConfig;
                }

                // Log what changed
                if ($existingConfig) {
                    $differences = $this->getConfigurationDifferences($existingConfig, $data);
                    Log::info('Configuration changes detected', [
                        'business_id' => $businessId,
                        'differences' => $differences
                    ]);
                }

                // Validate configuration values
                $this->validator->validate($data);

                // Prepare data for storage
                $configurationData = $this->prepareConfigurationData($data, $businessId);

                // Create or update main configuration
                $configuration = $this->saveConfiguration($businessId, $configurationData);

                // Sync tax rates
                Log::info('Starting tax rate sync...');
                $this->syncTaxRates($configuration, $data);

                // Sync terminal configuration
                Log::info('Starting terminal configuration sync...');
                $this->syncTerminalConfiguration($configuration, $data);

                Log::info('Configuration sync completed successfully', [
                    'business_id' => $businessId,
                    'configuration_id' => $configuration->id,
                    'global_version' => $configuration->global_version,
                    'terminal_version' => $configuration->terminal_version,
                    'taxpayer_version' => $configuration->taxpayer_version
                ]);

                return $configuration;
            });
        } catch (SyncException $e) {
            // Re-throw SyncException as-is
            throw $e;
        } catch (\Exception $e) {
            Log::error('Configuration sync failed', [
                'business_id' => $businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new SyncException(
                'Failed to sync EIS configuration: ' . $e->getMessage(),
                $this->determineStatusCode($e),
                $e
            );
        }
    }

    /**
     * Sync tax rates from EIS configuration.
     *
     * @param EisConfiguration $configuration
     * @param object $data
     * @return void
     */
    private function syncTaxRates(EisConfiguration $configuration, object $data): void
    {
        try {
            Log::debug('Entering syncTaxRates method', [
                'configuration_id' => $configuration->id,
                'business_id' => $configuration->business_id
            ]);

            // Get tax rates from global configuration
            $taxRates = $data->globalConfiguration->taxrates ?? [];
            
            Log::debug('Tax rates from response', [
                'tax_rates_count' => count($taxRates),
                'tax_rates' => $taxRates
            ]);
            
            if (empty($taxRates)) {
                Log::warning('No tax rates found in EIS configuration', [
                    'configuration_id' => $configuration->id,
                    'business_id' => $configuration->business_id,
                    'data_keys' => array_keys((array)$data->globalConfiguration ?? [])
                ]);
                return;
            }

            // Get activated tax rate IDs from taxpayer configuration
            $activatedTaxRateIds = $data->taxpayerConfiguration->activatedTaxRateIds ?? [];
            $activatedTaxRates = $data->taxpayerConfiguration->activatedTaxrates ?? [];

            Log::info('Syncing tax rates', [
                'configuration_id' => $configuration->id,
                'business_id' => $configuration->business_id,
                'total_tax_rates' => count($taxRates),
                'activated_count' => count($activatedTaxRateIds),
                'activated_tax_rates' => $activatedTaxRateIds
            ]);

            // Process each tax rate
            $syncedIds = [];
            $taxRateCount = 0;
            
            foreach ($taxRates as $taxRate) {
                $taxRateCount++;
                
                $syncedRate = $this->syncTaxRate(
                    $configuration,
                    $taxRate,
                    $activatedTaxRateIds,
                    $activatedTaxRates
                );
                $syncedIds[] = $syncedRate->id;
            }

            // Delete tax rates that are no longer in the response
            $this->cleanupTaxRates($configuration, $syncedIds);

            Log::info('Tax rates synced successfully', [
                'configuration_id' => $configuration->id,
                'business_id' => $configuration->business_id,
                'synced_count' => count($syncedIds),
                'tax_rate_ids' => $syncedIds
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to sync tax rates', [
                'configuration_id' => $configuration->id,
                'business_id' => $configuration->business_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to be handled by parent
            throw $e;
        }
    }

    /**
     * Sync a single tax rate.
     *
     * @param EisConfiguration $configuration
     * @param object $taxRate
     * @param array $activatedTaxRateIds
     * @param array $activatedTaxRates
     * @return TaxRate
     */
    private function syncTaxRate(
        EisConfiguration $configuration,
        object $taxRate,
        array $activatedTaxRateIds,
        array $activatedTaxRates
    ): EisTaxRate {
        // Check if this tax rate is activated
        $isActivated = in_array($taxRate->id, $activatedTaxRateIds);
        
        // Get activation details if available
        $activationDetails = null;
        foreach ($activatedTaxRates as $activated) {
            if (isset($activated->taxRateId) && $activated->taxRateId === $taxRate->id) {
                $activationDetails = $activated;
                break;
            }
        }

        // Prepare data for storage
        $data = [
            'business_id' => $configuration->business_id,
            'configuration_id' => $configuration->id,
            'tax_rate_id' => $taxRate->id,
            'name' => $taxRate->name ?? null,
            'charge_mode' => $taxRate->chargeMode ?? 'Item',
            'ordinal' => $taxRate->ordinal ?? 100,
            'rate' => $taxRate->rate ?? 0,
            'is_activated' => $isActivated,
            'activation_id' => $activationDetails->id ?? null,
        ];

        Log::debug('Saving tax rate data', [
            'tax_rate_id' => $taxRate->id,
            'data' => $data
        ]);

        // Update or create
        return EisTaxRate::updateOrCreate(
            [   'business_id' => $configuration->business_id,
                'configuration_id' => $configuration->id,
                'tax_rate_id' => $taxRate->id
            ],
            $data
        );
    }

    /**
     * Clean up tax rates that are no longer in the response.
     *
     * @param EisConfiguration $configuration
     * @param array $keepIds
     * @return void
     */
    private function cleanupTaxRates(EisConfiguration $configuration, array $keepIds): void
    {
        $deleted = EisTaxRate::where('configuration_id', $configuration->id)
            ->whereNotIn('id', $keepIds)
            ->delete();

        if ($deleted > 0) {
            Log::debug('Cleaned up old tax rates', [
                'configuration_id' => $configuration->id,
                'deleted_count' => $deleted
            ]);
        }
    }

    /**
     * Sync terminal configuration from EIS data.
     *
     * @param EisConfiguration $configuration
     * @param object $data
     * @return void
     */
    private function syncTerminalConfiguration(EisConfiguration $configuration, object $data): void
    {
        try {
            // Get terminal configuration from data
            $terminalData = $data->terminalConfiguration ?? null;
            
            if (!$terminalData) {
                Log::warning('No terminal configuration found in EIS data', [
                    'configuration_id' => $configuration->id,
                    'business_id' => $configuration->business_id
                ]);
                return;
            }

            Log::info('Syncing terminal configuration', [
                'configuration_id' => $configuration->id,
                'business_id' => $configuration->business_id,
                'version' => $terminalData->versionNo ?? null,
                'is_active' => $terminalData->isActiveTerminal ?? false
            ]);

            // Prepare terminal configuration data
            $terminalConfigData = [
                'configuration_id' => $configuration->id,
                'version' => $terminalData->versionNo ?? null,
                'terminal_label' => $terminalData->terminalLabel ?? null,
                'is_active' => $terminalData->isActiveTerminal ?? false,
                'email_address' => $terminalData->emailAddress ?? null,
                'phone_number' => $terminalData->phoneNumber ?? null,
                'trading_name' => $terminalData->tradingName ?? null,
                'address_lines' => isset($terminalData->addressLines) 
                    ? json_encode($terminalData->addressLines) 
                    : null,
                'raw_data' => json_encode($terminalData),
                'last_synced_at' => now()
            ];

            // Update or create terminal configuration
            $terminalConfig = EisTerminalConfiguration::updateOrCreate(
                ['configuration_id' => $configuration->id],
                $terminalConfigData
            );

            // Sync terminal site if present
            if (isset($terminalData->terminalSite)) {
                $this->syncTerminalSite($terminalConfig, $terminalData->terminalSite);
            }

            // Sync offline limit if present
            if (isset($terminalData->offlineLimit)) {
                $this->syncOfflineLimit($terminalConfig, $terminalData->offlineLimit);
            }

            Log::info('Terminal configuration synced successfully', [
                'configuration_id' => $configuration->id,
                'terminal_config_id' => $terminalConfig->id,
                'version' => $terminalConfig->version
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync terminal configuration', [
                'configuration_id' => $configuration->id,
                'business_id' => $configuration->business_id,
                'error' => $e->getMessage()
            ]);
            
            // Re-throw to be handled by parent
            throw $e;
        }
    }

    /**
     * Sync terminal site.
     *
     * @param TerminalConfiguration $terminalConfig
     * @param object $siteData
     * @return void
     */
    private function syncTerminalSite(EisTerminalConfiguration $terminalConfig, object $siteData): void
    {
        $siteData = [
            'terminal_configuration_id' => $terminalConfig->id,
            'site_id' => $siteData->siteId ?? null,
            'site_name' => $siteData->siteName ?? null,
            'raw_data' => json_encode($siteData),
            'last_synced_at' => now()
        ];

        EisTerminalSite::updateOrCreate(
            ['terminal_configuration_id' => $terminalConfig->id],
            $siteData
        );

        Log::debug('Terminal site synced', [
            'terminal_config_id' => $terminalConfig->id,
            'site_id' => $siteData['site_id'],
            'site_name' => $siteData['site_name']
        ]);
    }

    /**
     * Sync offline limit.
     *
     * @param TerminalConfiguration $terminalConfig
     * @param object $offlineLimitData
     * @return void
     */
    private function syncOfflineLimit(EisTerminalConfiguration $terminalConfig, object $offlineLimitData): void
    {
        $limitData = [
            'terminal_configuration_id' => $terminalConfig->id,
            'max_transaction_age_hours' => $offlineLimitData->maxTransactionAgeInHours ?? 72,
            'max_cumulative_amount' => $offlineLimitData->maxCummulativeAmount ?? 0,
            'raw_data' => json_encode($offlineLimitData),
            'last_synced_at' => now()
        ];

        EisOfflineLimit::updateOrCreate(
            ['terminal_configuration_id' => $terminalConfig->id],
            $limitData
        );

        Log::debug('Offline limit synced', [
            'terminal_config_id' => $terminalConfig->id,
            'max_hours' => $limitData['max_transaction_age_hours'],
            'max_amount' => $limitData['max_cumulative_amount']
        ]);
    }

    /**
     * Validate business ID.
     *
     * @param int $businessId
     * @throws \InvalidArgumentException
     */
    private function validateBusinessId(int $businessId): void
    {
        if ($businessId <= 0) {
            throw new \InvalidArgumentException('Invalid business ID: ' . $businessId);
        }
    }

    /**
     * Validate token.
     *
     * @param string $token
     * @throws \InvalidArgumentException
     */
    private function validateToken(string $token): void
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('Authentication token is empty');
        }

        // Optional: Validate token format (JWT format check)
        if (strpos($token, '.') === false || substr_count($token, '.') !== 2) {
            Log::warning('Token does not appear to be a valid JWT format', [
                'token_length' => strlen($token)
            ]);
        }
    }

    /**
     * Validate response status.
     *
     * @param EISConfigurationResponse $response
     * @throws SyncException
     */
    private function validateResponseStatus(EISConfigurationResponse $response): void
    {
        // Log the full response for debugging
        Log::debug('EIS API response validation', [
            'status_code' => $response->getStatusCode(),
            'remark' => $response->getRemark(),
            'is_success' => $response->isSuccess(),
            'has_errors' => $response->hasErrors(),
            'error_count' => count($response->getErrors())
        ]);

        if (!$response->isSuccess()) {
            $errorMessage = $response->getRemark() ?? 'Unknown error';
            $errors = $response->getErrors();
            
            Log::error('EIS API returned error response', [
                'status_code' => $response->getStatusCode(),
                'remark' => $response->getRemark(),
                'errors' => $errors,
                'has_validation_errors' => $response->hasValidationErrors(),
                'error_count' => count($errors)
            ]);

            // Build detailed error message
            if (!empty($errors)) {
                $errorDetails = [];
                foreach ($errors as $error) {
                    $field = $error->fieldName ?? 'general';
                    $message = $error->errorMessage ?? 'Unknown error';
                    $code = $error->errorCode ?? null;
                    
                    if ($code) {
                        $errorDetails[] = "[{$field}] {$message} (Code: {$code})";
                    } else {
                        $errorDetails[] = "[{$field}] {$message}";
                    }
                }
                $errorMessage .= ': ' . implode('; ', $errorDetails);
            }

            // Check for specific error types
            if ($response->hasValidationErrors()) {
                $validationErrors = $response->getValidationErrors();
                Log::warning('EIS API validation errors', $validationErrors);
                
                throw new SyncException(
                    'EIS API validation failed: ' . $errorMessage,
                    $response->getStatusCode() ?: 422
                );
            }

            throw new SyncException(
                'EIS API returned error: ' . $errorMessage,
                $response->getStatusCode() ?: 400
            );
        }

        // Check if data is complete
        if (!$response->hasCompleteData()) {
            Log::error('EIS API response missing required data', [
                'has_global' => isset($response->getData()->globalConfiguration),
                'has_terminal' => isset($response->getData()->terminalConfiguration),
                'has_taxpayer' => isset($response->getData()->taxpayerConfiguration),
                'data_keys' => array_keys((array)$response->getData())
            ]);

            throw new SyncException(
                'EIS API response missing required configuration data',
                500
            );
        }

        Log::info('EIS API response validation passed', [
            'status_code' => $response->getStatusCode(),
            'remark' => $response->getRemark(),
            'global_version' => $response->getGlobalConfiguration()->versionNo ?? null,
            'terminal_version' => $response->getTerminalConfiguration()->versionNo ?? null,
            'taxpayer_version' => $response->getTaxpayerConfiguration()->versionNo ?? null
        ]);
    }

    /**
     * Validate required response data.
     *
     * @param object $data
     * @throws SyncException
     */
    private function validateResponseData(object $data): void
    {
        foreach (self::REQUIRED_CONFIG_FIELDS as $field) {
            if (!isset($data->$field) || !is_object($data->$field)) {
                throw new SyncException(
                    "Missing or invalid required configuration field: {$field}"
                );
            }
        }
    }

    /**
     * Check if configuration has changed.
     *
     * @param EisConfiguration $existing
     * @param object $newData
     * @return bool
     */
    private function hasConfigurationChanged(EisConfiguration $existing, object $newData): bool
    {
        // Check version numbers
        $globalVersionChanged = $existing->global_version !== ($newData->globalConfiguration->versionNo ?? null);
        $terminalVersionChanged = $existing->terminal_version !== ($newData->terminalConfiguration->versionNo ?? null);
        $taxpayerVersionChanged = $existing->taxpayer_version !== ($newData->taxpayerConfiguration->versionNo ?? null);
        
        // Check TIN
        $tinChanged = $existing->tpin !== ($newData->taxpayerConfiguration->tin ?? null);
        
        // Check VAT registration status
        $vatChanged = $existing->is_vat_registered !== ($newData->taxpayerConfiguration->isVATRegistered ?? false);
        
        // Check tax office code
        $taxOfficeChanged = $existing->tax_office_code !== ($newData->taxpayerConfiguration->taxOfficeCode ?? null);
        
        return $globalVersionChanged || 
               $terminalVersionChanged || 
               $taxpayerVersionChanged ||
               $tinChanged ||
               $vatChanged ||
               $taxOfficeChanged;
    }

    /**
     * Prepare configuration data for storage.
     *
     * @param object $data
     * @param int $businessId
     * @return array
     */
    private function prepareConfigurationData(object $data, int $businessId): array
    {
        return [
            'business_id' => $businessId,
            'global_version' => $data->globalConfiguration->versionNo ?? null,
            'terminal_version' => $data->terminalConfiguration->versionNo ?? null,
            'taxpayer_version' => $data->taxpayerConfiguration->versionNo ?? null,
            'tpin' => $data->taxpayerConfiguration->tin ?? null,
            'is_vat_registered' => $data->taxpayerConfiguration->isVATRegistered ?? false,
            'tax_office_code' => $data->taxpayerConfiguration->taxOfficeCode ?? null,
            'tax_office_name' => $data->taxpayerConfiguration->taxOffice->name ?? null,
            'raw_response' => json_encode(['data' => $data]),
            'last_synced_at' => now()
        ];
    }

    /**
     * Save configuration to database.
     *
     * @param int $businessId
     * @param array $data
     * @return EisConfiguration
     */
    private function saveConfiguration(int $businessId, array $data): EisConfiguration
    {
        return EisConfiguration::updateOrCreate(
            ['business_id' => $businessId],
            $data
        );
    }

    /**
     * Determine status code from exception.
     *
     * @param \Exception $e
     * @return int
     */
    private function determineStatusCode(\Exception $e): int
    {
        // Get the code from the exception
        $code = $e->getCode();
        
        // If code is an integer and > 0, use it
        if (is_int($code) && $code > 0) {
            return $code;
        }
        
        // If code is a string, try to convert to int
        if (is_string($code) && is_numeric($code)) {
            $intCode = (int) $code;
            if ($intCode > 0) {
                return $intCode;
            }
        }

        // Check for specific error types in the message
        $message = strtolower($e->getMessage());
        
        foreach (self::NON_RETRYABLE_ERRORS as $error) {
            if (stripos($message, $error) !== false) {
                return 400; // Bad request
            }
        }

        // Check for HTTP status codes in the message
        if (preg_match('/status[:\s]+(\d{3})/i', $message, $matches)) {
            $statusCode = (int) $matches[1];
            if ($statusCode >= 100 && $statusCode <= 599) {
                return $statusCode;
            }
        }

        // Check for common HTTP status codes
        if (strpos($message, '404') !== false) {
            return 404;
        }
        if (strpos($message, '403') !== false) {
            return 403;
        }
        if (strpos($message, '401') !== false) {
            return 401;
        }
        if (strpos($message, '429') !== false) {
            return 429;
        }

        // Default to 500
        return 500;
    }

    /**
     * Check if an error is retryable.
     *
     * @param \Exception $e
     * @return bool
     */
    public function isRetryableError(\Exception $e): bool
    {
        $message = strtolower($e->getMessage());
        
        // Check for non-retryable errors
        foreach (self::NON_RETRYABLE_ERRORS as $error) {
            if (stripos($message, $error) !== false) {
                return false;
            }
        }

        // HTTP errors
        if ($e instanceof \Illuminate\Http\Client\RequestException) {
            $code = $e->getCode();
            $statusCode = is_int($code) ? $code : 0;
            
            // Server errors and rate limiting are retryable
            if ($statusCode >= 500 || $statusCode === 429) {
                return true;
            }
            
            // Client errors (4xx) are usually not retryable
            if ($statusCode >= 400 && $statusCode < 500) {
                return false;
            }
        }
        
        // Network/connection errors are retryable
        if ($e instanceof \Illuminate\Http\Client\ConnectionException) {
            return true;
        }
        
        // Timeout errors are retryable
        if ($e instanceof \Illuminate\Http\Client\TimeoutException) {
            return true;
        }
        
        // Database connection errors are retryable
        if ($e instanceof \PDOException || $e instanceof \Illuminate\Database\QueryException) {
            if (stripos($e->getMessage(), 'connection') !== false ||
                stripos($e->getMessage(), 'deadlock') !== false) {
                return true;
            }
        }
        
        // Default to retryable
        return true;
    }

    /**
     * Get configuration differences.
     *
     * @param EisConfiguration $existing
     * @param object $newData
     * @return array
     */
    public function getConfigurationDifferences(EisConfiguration $existing, object $newData): array
    {
        $differences = [];
        
        // Check global version
        if ($existing->global_version !== ($newData->globalConfiguration->versionNo ?? null)) {
            $differences['global_version'] = [
                'old' => $existing->global_version,
                'new' => $newData->globalConfiguration->versionNo ?? null
            ];
        }
        
        // Check terminal version
        if ($existing->terminal_version !== ($newData->terminalConfiguration->versionNo ?? null)) {
            $differences['terminal_version'] = [
                'old' => $existing->terminal_version,
                'new' => $newData->terminalConfiguration->versionNo ?? null
            ];
        }
        
        // Check taxpayer version
        if ($existing->taxpayer_version !== ($newData->taxpayerConfiguration->versionNo ?? null)) {
            $differences['taxpayer_version'] = [
                'old' => $existing->taxpayer_version,
                'new' => $newData->taxpayerConfiguration->versionNo ?? null
            ];
        }
        
        // Check TIN
        if ($existing->tpin !== ($newData->taxpayerConfiguration->tin ?? null)) {
            $differences['tpin'] = [
                'old' => $existing->tpin,
                'new' => $newData->taxpayerConfiguration->tin ?? null
            ];
        }
        
        // Check VAT registration
        if ($existing->is_vat_registered !== ($newData->taxpayerConfiguration->isVATRegistered ?? false)) {
            $differences['is_vat_registered'] = [
                'old' => $existing->is_vat_registered,
                'new' => $newData->taxpayerConfiguration->isVATRegistered ?? false
            ];
        }
        
        // Check tax office code
        if ($existing->tax_office_code !== ($newData->taxpayerConfiguration->taxOfficeCode ?? null)) {
            $differences['tax_office_code'] = [
                'old' => $existing->tax_office_code,
                'new' => $newData->taxpayerConfiguration->taxOfficeCode ?? null
            ];
        }
        
        return $differences;
    }

    /**
     * Force sync even if no changes detected.
     *
     * @param int $businessId
     * @param string $token
     * @return EisConfiguration
     * @throws SyncException
     */
    public function forceSync(int $businessId, string $token): EisConfiguration
    {
        Log::info('Forced configuration sync started', [
            'business_id' => $businessId
        ]);

        // Delete existing configuration to force full sync
        $existing = EisConfiguration::where('business_id', $businessId)->first();
        if ($existing) {
            Log::info('Deleting existing configuration for force sync', [
                'business_id' => $businessId,
                'configuration_id' => $existing->id
            ]);
            $existing->delete();
        }

        return $this->sync($businessId, $token);
    }

    /**
     * Get sync status for a business.
     *
     * @param int $businessId
     * @return array
     */
    public function getSyncStatus(int $businessId): array
    {
        $configuration = EisConfiguration::where('business_id', $businessId)->first();
        
        if (!$configuration) {
            return [
                'business_id' => $businessId,
                'synced' => false,
                'last_synced_at' => null,
                'message' => 'No configuration found for this business'
            ];
        }

        // Get tax rates count
        $taxRatesCount = EisTaxRate::where('configuration_id', $configuration->id)->count();
        $activatedTaxRates = EisTaxRate::where('configuration_id', $configuration->id)
            ->where('is_activated', true)
            ->count();
        
            Log::debug('Sync status retrieved', [
                'business_id' => $businessId,
                'configuration_id' => $configuration->id,
                'last_synced_at' => $configuration->last_synced_at,
                'global_version' => $configuration->global_version,
                'terminal_version' => $configuration->terminal_version,
                'taxpayer_version' => $configuration->taxpayer_version,
                'tax_rates_count' => $taxRatesCount,
                'activated_tax_rates' => $activatedTaxRates
            ]);

        // Get terminal info
        $terminal = EisTerminalConfiguration::where('configuration_id', $configuration->id)->first();

        return [
            'business_id' => $businessId,
            'synced' => true,
            'configuration_id' => $configuration->id,
            'last_synced_at' => $configuration->last_synced_at,
            'global_version' => $configuration->global_version,
            'terminal_version' => $configuration->terminal_version,
            'taxpayer_version' => $configuration->taxpayer_version,
            'tpin' => $configuration->tpin,
            'is_vat_registered' => $configuration->is_vat_registered,
            'tax_rates' => [
                'total' => $taxRatesCount,
                'activated' => $activatedTaxRates
            ],
            'terminal' => $terminal ? [
                'is_active' => $terminal->is_active,
                'trading_name' => $terminal->trading_name,
                'has_site' => EisTerminalSite::where('terminal_configuration_id', $terminal->id)->exists(),
                'has_offline_limit' => EisOfflineLimit::where('terminal_configuration_id', $terminal->id)->exists()
            ] : null,
            'hours_since_sync' => $configuration->last_synced_at 
                ? $configuration->last_synced_at->diffInHours(now()) 
                : null
        ];
    }

    /**
     * Get tax rates for a configuration.
     *
     * @param EisConfiguration $configuration
     * @param bool $onlyActivated
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTaxRates(EisConfiguration $configuration, bool $onlyActivated = false)
    {
        $query = EisTaxRate::where('configuration_id', $configuration->id);
        
        if ($onlyActivated) {
            $query->where('is_activated', true);
        }
        
        return $query->orderBy('ordinal')->orderBy('rate')->get();
    }

    /**
     * Get activated tax rate IDs.
     *
     * @param EisConfiguration $configuration
     * @return array
     */
    public function getActivatedTaxRateIds(EisConfiguration $configuration): array
    {
        return EisTaxRate::where('configuration_id', $configuration->id)
            ->where('is_activated', true)
            ->pluck('tax_rate_id')
            ->toArray();
    }

    /**
     * Get tax rate by ID.
     *
     * @param int $configurationId
     * @param string $taxRateId
     * @return TaxRate|null
     */
    public function getTaxRateById(int $configurationId, string $taxRateId): ?EisTaxRate
    {
        return EisTaxRate::where('configuration_id', $configurationId)
            ->where('tax_rate_id', $taxRateId)
            ->first();
    }

    /**
     * Calculate tax for a given amount.
     *
     * @param string $taxRateId
     * @param float $amount
     * @param int $configurationId
     * @return array
     */
    public function calculateTax(string $taxRateId, float $amount, int $configurationId): array
    {
        $taxRate = $this->getTaxRateById($configurationId, $taxRateId);

        if (!$taxRate) {
            return [
                'rate' => 0,
                'tax_amount' => 0,
                'total' => $amount,
                'tax_rate_id' => $taxRateId,
                'error' => 'Tax rate not found'
            ];
        }

        $taxAmount = ($amount * $taxRate->rate) / 100;
        
        return [
            'rate' => (float) $taxRate->rate,
            'tax_amount' => round($taxAmount, 2),
            'total' => round($amount + $taxAmount, 2),
            'tax_rate_id' => $taxRateId,
            'name' => $taxRate->name,
            'is_activated' => $taxRate->is_activated,
            'charge_mode' => $taxRate->charge_mode
        ];
    }

    /**
     * Get terminal configuration.
     *
     * @param EisConfiguration $configuration
     * @return TerminalConfiguration|null
     */
    public function getTerminalConfiguration(EisConfiguration $configuration): ?EisTerminalConfiguration
    {
        return EisTerminalConfiguration::where('configuration_id', $configuration->id)
            ->with(['terminalSite', 'offlineLimit'])
            ->first();
    }

    /**
     * Get tax rates summary.
     *
     * @param EisConfiguration $configuration
     * @return array
     */
    public function getTaxRatesSummary(EisConfiguration $configuration): array
    {
        $total = EisTaxRate::where('configuration_id', $configuration->id)->count();
        $activated = EisTaxRate::where('configuration_id', $configuration->id)
            ->where('is_activated', true)
            ->count();
        
        $rates = EisTaxRate::where('configuration_id', $configuration->id)
            ->orderBy('rate')
            ->get(['tax_rate_id', 'name', 'rate', 'is_activated']);

        return [
            'total' => $total,
            'activated' => $activated,
            'inactive' => $total - $activated,
            'highest_rate' => EisTaxRate::where('configuration_id', $configuration->id)
                ->where('is_activated', true)
                ->max('rate'),
            'lowest_rate' => EisTaxRate::where('configuration_id', $configuration->id)
                ->where('is_activated', true)
                ->min('rate'),
            'rates' => $rates->toArray()
        ];
    }
}