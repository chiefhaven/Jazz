<?php

namespace Modules\EIS\Services\Configuration;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Exceptions\SyncException;
use Modules\EIS\Services\Configuration\EISConfigurationResponse;
use Modules\EIS\Services\Configuration\Validators\ConfigurationValidator;
use Modules\EIS\Services\Configuration\TaxConfigurationService;
use Modules\EIS\Services\Configuration\TerminalConfigurationService;

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
        'tin not found',
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

                // Create or update configuration
                $configuration = $this->saveConfiguration($businessId, $configurationData);

                // Sync related configurations
                $this->syncRelatedConfigurations($configuration, $data);

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
            // Don't throw, just warn - the API will validate it
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
        if (!$response->isSuccess()) {
            $errorMessage = $response->getRemark() ?? 'Unknown error';
            $errors = $response->getErrors();
            
            // Log detailed error information
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

        Log::debug('EIS API response validation passed', [
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
        $tinChanged = $existing->tin !== ($newData->taxpayerConfiguration->tin ?? null);
        
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
            'tin' => $data->taxpayerConfiguration->tin ?? null,
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
     * Sync related configurations.
     *
     * @param EisConfiguration $configuration
     * @param object $data
     * @throws SyncException
     */
    private function syncRelatedConfigurations(
        EisConfiguration $configuration,
        object $data
    ): void {
        $services = [
            'tax' => TaxConfigurationService::class,
            'terminal' => TerminalConfigurationService::class
        ];

        $failedServices = [];
        $successfulServices = [];
        
        foreach ($services as $name => $class) {
            try {
                app($class)->sync($configuration, $data);
                $successfulServices[] = $name;
                Log::debug("{$name} configuration synced successfully", [
                    'configuration_id' => $configuration->id
                ]);
            } catch (\Exception $e) {
                $failedServices[] = $name;
                Log::error("Failed to sync {$name} configuration", [
                    'configuration_id' => $configuration->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Continue with other services instead of throwing immediately
                // This allows partial success
            }
        }

        // If all services failed, throw an exception
        if (!empty($failedServices) && count($failedServices) === count($services)) {
            throw new SyncException(
                'Failed to sync all related configurations: ' . implode(', ', $failedServices)
            );
        }

        // Log warning if some services failed
        if (!empty($failedServices)) {
            Log::warning('Some related configurations failed to sync', [
                'configuration_id' => $configuration->id,
                'failed_services' => $failedServices,
                'successful_services' => $successfulServices
            ]);
        }

        // Log summary
        Log::info('Related configurations sync summary', [
            'configuration_id' => $configuration->id,
            'successful' => $successfulServices,
            'failed' => $failedServices
        ]);
    }

    /**
     * Determine status code from exception.
     *
     * @param \Exception $e
     * @return int
     */
    private function determineStatusCode(\Exception $e): int
    {
        // If exception already has a code, use it
        if ($e->getCode() > 0) {
            return $e->getCode();
        }

        // Check for specific error types
        $message = strtolower($e->getMessage());
        
        foreach (self::NON_RETRYABLE_ERRORS as $error) {
            if (stripos($message, $error) !== false) {
                return 400; // Bad request
            }
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
            $statusCode = $e->getCode();
            
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
        if ($existing->tin !== ($newData->taxpayerConfiguration->tin ?? null)) {
            $differences['tin'] = [
                'old' => $existing->tin,
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

        // Temporarily disable the change detection by deleting the existing config
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

        return [
            'business_id' => $businessId,
            'synced' => true,
            'configuration_id' => $configuration->id,
            'last_synced_at' => $configuration->last_synced_at,
            'global_version' => $configuration->global_version,
            'terminal_version' => $configuration->terminal_version,
            'taxpayer_version' => $configuration->taxpayer_version,
            'tin' => $configuration->tin,
            'is_vat_registered' => $configuration->is_vat_registered,
            'hours_since_sync' => $configuration->last_synced_at 
                ? $configuration->last_synced_at->diffInHours(now()) 
                : null
        ];
    }
}