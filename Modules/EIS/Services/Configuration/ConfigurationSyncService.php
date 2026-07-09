<?php

namespace Modules\EIS\Services\Configuration;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Services\Configuration\Exceptions\SyncException;
use Modules\EIS\Services\Configuration\EISConfigurationResponse;
use Modules\EIS\Services\Configuration\Validators\ConfigurationValidator;

class ConfigurationSyncService
{
    private const REQUIRED_CONFIG_FIELDS = [
        'globalConfiguration',
        'terminalConfiguration',
        'taxpayerConfiguration'
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

                // Validate response status
                $this->validateResponseStatus($response);

                // Get and validate data
                $data = $response->getData();
                $this->validateResponseData($data);

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
        } catch (\Exception $e) {
            Log::error('Configuration sync failed', [
                'business_id' => $businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new SyncException(
                'Failed to sync EIS configuration: ' . $e->getMessage(),
                $e->getCode(),
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
            
            if (!empty($errors)) {
                $errorMessage .= ': ' . json_encode($errors);
            }

            throw new SyncException(
                'EIS API returned error: ' . $errorMessage,
                $response->getStatusCode()
            );
        }
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

        foreach ($services as $name => $class) {
            try {
                app($class)->sync($configuration, $data);
                Log::debug("{$name} configuration synced successfully", [
                    'configuration_id' => $configuration->id
                ]);
            } catch (\Exception $e) {
                throw new SyncException(
                    "Failed to sync {$name} configuration: " . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }
    }
}