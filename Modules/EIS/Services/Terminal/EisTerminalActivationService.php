<?php

namespace Modules\EIS\Services\Terminal;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Models\EisTerminalConfiguration;
use Modules\EIS\Models\EisTerminalSite;
use Modules\EIS\Models\EisOfflineLimit;
use Modules\EIS\Models\EisTaxRate;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Configuration\ConfigurationSyncService;

class EisTerminalActivationService
{
    protected string $apiBaseUrl;

    public function __construct(
        protected ConfigurationSyncService $syncService
    ) {
        $this->apiBaseUrl = config('eis.base_url', 'https://api.eis.example.com');
    }

    /**
     * Activate terminal via EIS API - No token required.
     *
     * @param int $businessId
     * @param string $activationCode
     * @param array $environment
     * @param int|null $activatedBy
     * @return array
     */
    public function activateTerminal(
        int $businessId,
        string $activationCode,
        array $environment = [],
        ?int $activatedBy = null
    ): array {
        try {
            Log::info('Terminal activation started', [
                'business_id' => $businessId,
                'activation_code' => $activationCode
            ]);

            // Step 1: Call EIS API to activate terminal
            $activationResponse = $this->callActivationAPI($businessId, $activationCode, $environment);

            // Step 2: Process activation response and save to database
            $result = $this->processActivationResponse(
                $businessId,
                $activationResponse,
                $activationCode,
                $environment,
                $activatedBy
            );

            // Step 3: After successful activation and database sync, send confirmation
            if ($result['success']) {
                $terminalId = $result['data']['terminal_id'] ?? null;
                $secretKey = $result['terminal_credentials']['secretKey'] ?? null;
                
                if ($terminalId && $secretKey) {
                    $this->sendActivationConfirmation($terminalId, $activationCode, $secretKey);
                } else {
                    Log::warning('Missing terminal ID or secret key for confirmation', [
                        'terminal_id' => $terminalId,
                        'has_secret_key' => !empty($secretKey)
                    ]);
                }
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Terminal activation failed', [
                'business_id' => $businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to activate terminal: ' . $e->getMessage(),
                'status_code' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Send activation confirmation to EIS API with X-Signature.
     * Signature uses terminal_activation_code and secretKey from response.
     *
     * @param string $terminalId
     * @param string $activationCode
     * @param string $secretKey
     * @return bool
     */
    private function sendActivationConfirmation(string $terminalId, string $activationCode, string $secretKey): bool
    {
        try {
            $url = $this->apiBaseUrl . '/onboarding/terminal-activated-confirmation';

            $payload = [
                'terminalId' => $terminalId
            ];

            // Compute signature using terminal_activation_code and secretKey from response
            $signature = $this->computeXSignature($activationCode, $secretKey);

            Log::debug('Sending activation confirmation', [
                'url' => $url,
                'terminal_id' => $terminalId,
                'signature' => $signature,
                'payload' => $payload
            ]);

            $response = Http::acceptJson()
                ->withHeaders([
                    'X-Signature' => $signature
                ])
                ->timeout(30)
                ->post($url, $payload);

            if (!$response->successful()) {
                Log::error('Activation confirmation failed', [
                    'terminal_id' => $terminalId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

            $responseData = $response->json();

            Log::info('Activation confirmation sent successfully', [
                'terminal_id' => $terminalId,
                'response' => $responseData
            ]);

            // Update terminal as confirmed
            $terminal = EisTerminalConfiguration::where('terminal_id', $terminalId)->first();
            if ($terminal) {
                $terminal->update([
                    'is_confirmed' => true,
                    'confirmed_at' => now(),
                    'confirmation_response' => json_encode($responseData)
                ]);
            }

            // Update eis_settings with terminal_id and secret_key
            $this->updateEisSettings($terminalId, $secretKey, $terminal);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send activation confirmation', [
                'terminal_id' => $terminalId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Update eis_settings table with terminal details.
     * Creates new record if not exists.
     *
     * @param string $terminalId
     * @param string $secretKey
     * @param EisTerminalConfiguration $terminal
     * @return void
     */
    private function updateEisSettings(string $terminalId, string $secretKey, EisTerminalConfiguration $terminal): void
    {
        try {
            $businessId = $terminal->configuration->business_id;
            
            // Find or create eis_setting record
            $setting = EisSetting::where('business_id', $businessId)->first();
            
            if ($setting) {
                // Update existing record
                $setting->update([
                    'device_id' => $terminalId,
                    'secret_key' => $secretKey,
                    'tpin' => $terminal->taxpayer_id ?? $setting->tpin,
                    'last_sync_at' => now(),
                    'sync_status' => 'success',
                    'sync_error' => null,
                ]);
                
                Log::info('EIS settings updated after activation', [
                    'business_id' => $businessId,
                    'device_id' => $terminalId,
                    'has_secret_key' => !empty($secretKey)
                ]);
            } else {
                // Create new record if not exists
                $setting = EisSetting::create([
                    'business_id' => $businessId,
                    'device_id' => $terminalId,
                    'secret_key' => $secretKey,
                    'jwt_token' => $jwtToken ?? null,
                    'tpin' => $terminal->taxpayer_id ?? null,
                    'status' => true,
                    'sync_status' => 'success',
                    'last_sync_at' => now(),
                    'successful_syncs' => 1,
                    'failed_syncs' => 0,
                ]);
                
                Log::info('EIS settings created after activation', [
                    'business_id' => $businessId,
                    'device_id' => $terminalId,
                    'has_secret_key' => !empty($secretKey)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update/create EIS settings', [
                'business_id' => $terminal->configuration->business_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Compute X-Signature using SHA-512 as per MRA specification.
     * Signature = base64_encode(hash_hmac('sha512', activationCode, secretKey, true))
     *
     * @param string $activationCode
     * @param string $secretKey
     * @return string
     */
    private function computeXSignature(string $activationCode, string $secretKey): string
    {
        // HMAC-SHA512 hash of the activation code using the secret key
        $hash = hash_hmac('sha512', $activationCode, $secretKey, true);
        // Base64 encode the hash
        return base64_encode($hash);
    }

    /**
     * Call EIS activation API with correct payload structure.
     * statusCode: 1 is considered success.
     *
     * @param int $businessId
     * @param string $activationCode
     * @param array $environment
     * @return object
     * @throws \Exception
     */
    private function callActivationAPI(int $businessId, string $activationCode, array $environment): object
    {
        $url = $this->apiBaseUrl . '/onboarding/activate-terminal';

        // Build the correct payload structure
        $payload = [
            'terminalActivationCode' => $activationCode,
            'environment' => [
                'platform' => [
                    'osName' => $environment['platform']['osName'] ?? 'Unknown',
                    'osVersion' => $environment['platform']['osVersion'] ?? 'Unknown',
                    'osBuild' => $environment['platform']['osBuild'] ?? '',
                    'macAddress' => $environment['platform']['macAddress'] ?? '00:00:00:00:00:00'
                ],
                'pos' => [
                    'productID' => $environment['pos']['productID'] ?? config('app.name', 'POS System'),
                    'productVersion' => $environment['pos']['productVersion'] ?? config('app.version', '1.0.0')
                ]
            ]
        ];

        Log::debug('Calling EIS activation API', [
            'url' => $url,
            'business_id' => $businessId,
            'payload' => $payload
        ]);

        // No authentication token required
        $response = Http::acceptJson()
            ->timeout(60)
            ->post($url, $payload);

        if (!$response->successful()) {
            Log::error('EIS activation API error', [
                'status' => $response->status(),
                'response' => $response->body(),
                'business_id' => $businessId
            ]);

            throw new \Exception('EIS activation API failed: ' . $response->body(), $response->status());
        }

        $responseData = $response->object();

        Log::debug('EIS activation API response', [
            'business_id' => $businessId,
            'response' => $responseData
        ]);

        // Check if response has error (statusCode: 1 = success, other = error)
        if (isset($responseData->statusCode) && $responseData->statusCode !== 1) {
            $errorMessage = $responseData->remark ?? 'Unknown error';
            
            if (!empty($responseData->errors)) {
                $errors = [];
                foreach ($responseData->errors as $error) {
                    $errors[] = $error->errorMessage ?? 'Unknown error';
                }
                $errorMessage .= ': ' . implode(', ', $errors);
            }

            throw new \Exception($errorMessage, $responseData->statusCode);
        }

        // Check if data exists
        if (!isset($responseData->data) || !isset($responseData->data->activatedTerminal)) {
            Log::error('EIS activation API response missing data', [
                'business_id' => $businessId,
                'response' => $responseData
            ]);
            throw new \Exception('EIS activation API response missing terminal data');
        }

        return $responseData;
    }

    /**
     * Process activation response and save data.
     *
     * @param int $businessId
     * @param object $response
     * @param string $activationCode
     * @param array $environment
     * @param int|null $activatedBy
     * @return array
     */
    private function processActivationResponse(
        int $businessId,
        object $response,
        string $activationCode,
        array $environment,
        ?int $activatedBy
    ): array {
        try {
            return DB::transaction(function () use ($businessId, $response, $activationCode, $environment, $activatedBy) {
                // Get activated terminal data
                $activatedTerminal = $response->data->activatedTerminal ?? null;
                $configurationData = $response->data->configuration ?? null;

                if (!$activatedTerminal) {
                    Log::error('No activated terminal data in response', [
                        'business_id' => $businessId,
                        'response' => json_encode($response)
                    ]);
                    throw new \Exception('No activated terminal data in response');
                }

                if (!$configurationData) {
                    Log::error('No configuration data in response', [
                        'business_id' => $businessId,
                        'response' => json_encode($response)
                    ]);
                    throw new \Exception('No configuration data in response');
                }

                Log::info('Processing activation response', [
                    'business_id' => $businessId,
                    'terminal_id' => $activatedTerminal->terminalId ?? null,
                    'taxpayer_id' => $activatedTerminal->taxpayerId ?? null,
                    'has_credentials' => isset($activatedTerminal->terminalCredentials)
                ]);

                // Save or update main configuration
                $configuration = $this->saveConfiguration($businessId, $configurationData);

                // Save or update terminal configuration
                $terminal = $this->saveTerminalConfiguration(
                    $configuration,
                    $configurationData->terminalConfiguration ?? null,
                    $activatedTerminal,
                    $activationCode,
                    $environment,
                    $activatedBy
                );

                // Sync tax rates
                if (isset($configurationData->globalConfiguration)) {
                    $this->syncTaxRates($configuration, $configurationData);
                }

                Log::info('Terminal activated and saved successfully', [
                    'business_id' => $businessId,
                    'terminal_config_id' => $terminal->id,
                    'terminal_id' => $activatedTerminal->terminalId ?? null,
                    'activated_by' => $activatedBy
                ]);

                // Get terminal credentials for response
                $terminalCredentials = null;
                if (isset($activatedTerminal->terminalCredentials)) {
                    $terminalCredentials = [
                        'jwtToken' => $activatedTerminal->terminalCredentials->jwtToken ?? null,
                        'secretKey' => $activatedTerminal->terminalCredentials->secretKey ?? null
                    ];
                }

                return [
                    'success' => true,
                    'message' => $response->remark ?? 'Terminal activated successfully',
                    'data' => $this->getTerminalDetails($terminal, $activatedTerminal),
                    'terminal_credentials' => $terminalCredentials,
                    'activation_code' => $activationCode,
                    'status_code' => $response->statusCode ?? 1
                ];
            });
        } catch (\Exception $e) {
            Log::error('Failed to process activation response', [
                'business_id' => $businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Generate activation code.
     *
     * @param int $businessId
     * @return string
     */
    private function generateActivationCode(int $businessId): string
    {
        return 'TAC-' . strtoupper(uniqid() . '-' . $businessId);
    }

    /**
     * Save configuration from activation response.
     *
     * @param int $businessId
     * @param object $configurationData
     * @return EisConfiguration
     */
    private function saveConfiguration(int $businessId, object $configurationData): EisConfiguration
    {
        $globalConfig = $configurationData->globalConfiguration ?? null;
        $terminalConfig = $configurationData->terminalConfiguration ?? null;
        $taxpayerConfig = $configurationData->taxpayerConfiguration ?? null;

        $data = [
            'business_id' => $businessId,
            'global_version' => $globalConfig->versionNo ?? null,
            'terminal_version' => $terminalConfig->versionNo ?? null,
            'taxpayer_version' => $taxpayerConfig->versionNo ?? null,
            'tpin' => $taxpayerConfig->tin ?? null,
            'is_vat_registered' => $taxpayerConfig->isVATRegistered ?? false,
            'tax_office_code' => $taxpayerConfig->taxOfficeCode ?? null,
            'tax_office_name' => $taxpayerConfig->taxOffice->name ?? null,
            'raw_response' => json_encode(['data' => $configurationData]),
            'last_synced_at' => now()
        ];

        return EisConfiguration::updateOrCreate(
            ['business_id' => $businessId],
            $data
        );
    }

    /**
     * Save terminal configuration from activation response.
     *
     * @param EisConfiguration $configuration
     * @param object|null $terminalData
     * @param object $activatedTerminal
     * @param string $activationCode
     * @param array $environment
     * @param int|null $activatedBy
     * @return EisTerminalConfiguration
     */
    private function saveTerminalConfiguration(
        EisConfiguration $configuration,
        ?object $terminalData,
        object $activatedTerminal,
        string $activationCode,
        array $environment,
        ?int $activatedBy
    ): EisTerminalConfiguration {
        // Prepare terminal configuration data
        $data = [
            'configuration_id' => $configuration->id,
            'version' => $terminalData->versionNo ?? null,
            'terminal_label' => $terminalData->terminalLabel ?? null,
            'is_active' => true,
            'is_confirmed' => false,
            'email_address' => $terminalData->emailAddress ?? null,
            'phone_number' => $terminalData->phoneNumber ?? null,
            'trading_name' => $terminalData->tradingName ?? null,
            'address_lines' => isset($terminalData->addressLines) 
                ? json_encode($terminalData->addressLines) 
                : null,
            'raw_data' => json_encode($terminalData),
            'last_synced_at' => now(),
            // Activation fields
            'activated_at' => now(),
            'activated_by' => $activatedBy,
            'deactivated_at' => null,
            'deactivated_by' => null,
            'deactivation_reason' => null,
            'toggled_at' => now(),
            'toggled_by' => $activatedBy,
            'activation_code' => $activationCode,
            'activation_environment' => json_encode($environment),
            // Terminal details from response
            'terminal_id' => $activatedTerminal->terminalId ?? null,
            'terminal_position' => $activatedTerminal->terminalPosition ?? 0,
            'taxpayer_id' => $activatedTerminal->taxpayerId ?? null,
            'activation_date' => $activatedTerminal->activationDate ?? now(),
            'jwt_token' => $activatedTerminal->terminalCredentials->jwtToken ?? null,
            'secret_key' => $activatedTerminal->terminalCredentials->secretKey ?? null,
        ];

        // Update or create terminal configuration
        return EisTerminalConfiguration::updateOrCreate(
            ['configuration_id' => $configuration->id],
            $data
        );
    }

    /**
     * Sync tax rates from configuration data.
     *
     * @param EisConfiguration $configuration
     * @param object $configurationData
     * @return void
     */
    private function syncTaxRates(EisConfiguration $configuration, object $configurationData): void
    {
        $taxRates = $configurationData->globalConfiguration->taxrates ?? [];
        $activatedTaxRateIds = $configurationData->taxpayerConfiguration->activatedTaxRateIds ?? [];
        $activatedTaxRates = $configurationData->taxpayerConfiguration->activatedTaxrates ?? [];

        if (empty($taxRates)) {
            Log::warning('No tax rates found in activation response', [
                'configuration_id' => $configuration->id
            ]);
            return;
        }

        Log::info('Syncing tax rates from activation', [
            'configuration_id' => $configuration->id,
            'total_tax_rates' => count($taxRates),
            'activated_count' => count($activatedTaxRateIds)
        ]);

        $syncedIds = [];
        foreach ($taxRates as $taxRate) {
            $isActivated = in_array($taxRate->id, $activatedTaxRateIds);
            
            $activationDetails = null;
            foreach ($activatedTaxRates as $activated) {
                if (isset($activated->taxRateId) && $activated->taxRateId === $taxRate->id) {
                    $activationDetails = $activated;
                    break;
                }
            }

            $rate = EisTaxRate::updateOrCreate(
                [
                    'configuration_id' => $configuration->id,
                    'tax_rate_id' => $taxRate->id
                ],
                [
                    'name' => $taxRate->name ?? null,
                    'charge_mode' => $taxRate->chargeMode ?? 'Item',
                    'ordinal' => $taxRate->ordinal ?? 100,
                    'rate' => $taxRate->rate ?? 0,
                    'is_activated' => $isActivated,
                    'activation_id' => $activationDetails->id ?? null,
                ]
            );
            
            $syncedIds[] = $rate->id;
        }

        // Clean up old tax rates
        EisTaxRate::where('configuration_id', $configuration->id)
            ->whereNotIn('id', $syncedIds)
            ->delete();

        Log::debug('Tax rates synced from activation', [
            'configuration_id' => $configuration->id,
            'synced_count' => count($syncedIds)
        ]);
    }

    /**
     * Get terminal details.
     *
     * @param EisTerminalConfiguration $terminal
     * @param object|null $activatedTerminal
     * @return array
     */
    private function getTerminalDetails(EisTerminalConfiguration $terminal, ?object $activatedTerminal = null): array
    {
        $details = [
            'id' => $terminal->id,
            'configuration_id' => $terminal->configuration_id,
            'terminal_label' => $terminal->terminal_label,
            'is_active' => $terminal->is_active,
            'is_confirmed' => $terminal->is_confirmed ?? false,
            'status' => $terminal->is_active ? 'Active' : 'Inactive',
            'trading_name' => $terminal->trading_name,
            'email_address' => $terminal->email_address,
            'phone_number' => $terminal->phone_number,
            'version' => $terminal->version,
            'address' => $terminal->full_address ?? '',
            'activated_at' => $terminal->activated_at,
            'activated_by' => $terminal->activated_by,
            'deactivated_at' => $terminal->deactivated_at,
            'deactivated_by' => $terminal->deactivated_by,
            'deactivation_reason' => $terminal->deactivation_reason,
            'toggled_at' => $terminal->toggled_at,
            'toggled_by' => $terminal->toggled_by,
            'last_synced_at' => $terminal->last_synced_at,
            'activation_code' => $terminal->activation_code,
            'activation_environment' => null,
            'terminal_id' => $terminal->terminal_id,
            'terminal_position' => $terminal->terminal_position,
            'taxpayer_id' => $terminal->taxpayer_id,
            'activation_date' => $terminal->activation_date,
            'confirmed_at' => $terminal->confirmed_at,
            'confirmation_response' => $terminal->confirmation_response ? $terminal->confirmation_response : null,
        ];

        // Add site details
        if ($terminal->terminalSite) {
            $details['site'] = [
                'site_id' => $terminal->terminalSite->site_id,
                'site_name' => $terminal->terminalSite->site_name
            ];
        } else {
            $details['site'] = null;
        }

        // Add offline limit details
        if ($terminal->offlineLimit) {
            $details['offline_limit'] = [
                'max_transaction_age_hours' => $terminal->offlineLimit->max_transaction_age_hours,
                'max_cumulative_amount' => $terminal->offlineLimit->max_cumulative_amount
            ];
        } else {
            $details['offline_limit'] = null;
        }

        // Add terminal credentials if available
        if ($activatedTerminal && isset($activatedTerminal->terminalCredentials)) {
            $details['terminal_credentials'] = [
                'jwt_token' => $activatedTerminal->terminalCredentials->jwtToken ?? null,
                'secret_key' => $activatedTerminal->terminalCredentials->secretKey ?? null
            ];
        } elseif ($terminal->jwt_token || $terminal->secret_key) {
            $details['terminal_credentials'] = [
                'jwt_token' => $terminal->jwt_token,
                'secret_key' => $terminal->secret_key
            ];
        }

        return $details;
    }

    /**
     * Deactivate a terminal.
     *
     * @param int $businessId
     * @param string|null $reason
     * @param int|null $deactivatedBy
     * @return array
     */
    public function deactivate(int $businessId, ?string $reason = null, ?int $deactivatedBy = null): array
    {
        try {
            Log::info('Terminal deactivation requested', [
                'business_id' => $businessId,
                'reason' => $reason
            ]);

            $configuration = EisConfiguration::where('business_id', $businessId)->first();
            
            if (!$configuration) {
                return [
                    'success' => false,
                    'message' => 'Configuration not found for this business'
                ];
            }

            $terminal = EisTerminalConfiguration::where('configuration_id', $configuration->id)->first();
            
            if (!$terminal) {
                return [
                    'success' => false,
                    'message' => 'Terminal configuration not found'
                ];
            }

            if (!$terminal->is_active) {
                return [
                    'success' => true,
                    'message' => 'Terminal is already inactive',
                    'data' => $this->getTerminalDetails($terminal)
                ];
            }

            $terminal->update([
                'is_active' => false,
                'deactivated_at' => now(),
                'deactivated_by' => $deactivatedBy,
                'deactivation_reason' => $reason,
                'toggled_at' => now(),
                'toggled_by' => $deactivatedBy
            ]);

            Log::info('Terminal deactivated successfully', [
                'business_id' => $businessId,
                'terminal_config_id' => $terminal->id
            ]);

            return [
                'success' => true,
                'message' => 'Terminal deactivated successfully',
                'data' => $this->getTerminalDetails($terminal)
            ];

        } catch (\Exception $e) {
            Log::error('Terminal deactivation failed', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to deactivate terminal: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Toggle terminal activation status.
     *
     * @param int $businessId
     * @param string $token
     * @param int|null $toggledBy
     * @return array
     */
    public function toggle(int $businessId, string $token, ?int $toggledBy = null): array
    {
        try {
            Log::info('Terminal activation toggle requested', [
                'business_id' => $businessId
            ]);

            $configuration = EisConfiguration::where('business_id', $businessId)->first();
            
            if (!$configuration) {
                return [
                    'success' => false,
                    'message' => 'Configuration not found'
                ];
            }

            $terminal = EisTerminalConfiguration::where('configuration_id', $configuration->id)->first();
            
            if (!$terminal) {
                return [
                    'success' => false,
                    'message' => 'Terminal configuration not found'
                ];
            }

            $newStatus = !$terminal->is_active;
            
            $updateData = [
                'is_active' => $newStatus,
                'toggled_at' => now(),
                'toggled_by' => $toggledBy
            ];

            if ($newStatus) {
                $updateData['activated_at'] = now();
                $updateData['activated_by'] = $toggledBy;
                $updateData['deactivated_at'] = null;
                $updateData['deactivated_by'] = null;
                $updateData['deactivation_reason'] = null;
            } else {
                $updateData['deactivated_at'] = now();
                $updateData['deactivated_by'] = $toggledBy;
            }

            $terminal->update($updateData);

            Log::info('Terminal status toggled', [
                'business_id' => $businessId,
                'terminal_config_id' => $terminal->id,
                'new_status' => $newStatus ? 'active' : 'inactive'
            ]);

            return [
                'success' => true,
                'message' => 'Terminal ' . ($newStatus ? 'activated' : 'deactivated') . ' successfully',
                'data' => $this->getTerminalDetails($terminal)
            ];

        } catch (\Exception $e) {
            Log::error('Terminal toggle failed', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to toggle terminal: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get terminal status.
     *
     * @param int $businessId
     * @return array
     */
    public function getStatus(int $businessId): array
    {
        try {
            $configuration = EisConfiguration::where('business_id', $businessId)->first();
            
            if (!$configuration) {
                return [
                    'success' => false,
                    'message' => 'Configuration not found',
                    'is_active' => false
                ];
            }

            $terminal = EisTerminalConfiguration::where('configuration_id', $configuration->id)->first();
            
            if (!$terminal) {
                return [
                    'success' => false,
                    'message' => 'Terminal configuration not found',
                    'is_active' => false
                ];
            }

            return [
                'success' => true,
                'is_active' => $terminal->is_active,
                'is_confirmed' => $terminal->is_confirmed ?? false,
                'data' => $this->getTerminalDetails($terminal)
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get terminal status', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get terminal status: ' . $e->getMessage(),
                'is_active' => false
            ];
        }
    }

    /**
     * Check if terminal is active.
     *
     * @param int $businessId
     * @return bool
     */
    public function isActive(int $businessId): bool
    {
        $status = $this->getStatus($businessId);
        return $status['is_active'] ?? false;
    }

    /**
     * Get terminal activation history.
     *
     * @param int $businessId
     * @return array
     */
    public function getActivationHistory(int $businessId): array
    {
        try {
            $configuration = EisConfiguration::where('business_id', $businessId)->first();
            
            if (!$configuration) {
                return [
                    'success' => false,
                    'message' => 'Configuration not found'
                ];
            }

            $terminal = EisTerminalConfiguration::where('configuration_id', $configuration->id)->first();
            
            if (!$terminal) {
                return [
                    'success' => false,
                    'message' => 'Terminal configuration not found'
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'activated_at' => $terminal->activated_at,
                    'activated_by' => $terminal->activated_by,
                    'deactivated_at' => $terminal->deactivated_at,
                    'deactivated_by' => $terminal->deactivated_by,
                    'deactivation_reason' => $terminal->deactivation_reason,
                    'toggled_at' => $terminal->toggled_at,
                    'toggled_by' => $terminal->toggled_by,
                    'current_status' => $terminal->is_active ? 'Active' : 'Inactive',
                    'is_confirmed' => $terminal->is_confirmed ?? false,
                    'confirmed_at' => $terminal->confirmed_at,
                    'terminal_id' => $terminal->terminal_id,
                    'activation_date' => $terminal->activation_date
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get activation history', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get activation history: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Regenerate terminal credentials.
     *
     * @param int $businessId
     * @param string $token
     * @return array
     */
    public function regenerateCredentials(int $businessId, string $token): array
    {
        try {
            Log::info('Regenerating terminal credentials', [
                'business_id' => $businessId
            ]);

            $configuration = EisConfiguration::where('business_id', $businessId)->first();
            
            if (!$configuration) {
                return [
                    'success' => false,
                    'message' => 'Configuration not found'
                ];
            }

            $terminal = EisTerminalConfiguration::where('configuration_id', $configuration->id)->first();
            
            if (!$terminal) {
                return [
                    'success' => false,
                    'message' => 'Terminal configuration not found'
                ];
            }

            $url = $this->apiBaseUrl . '/onboarding/regenerate-credentials';
            
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(60)
                ->post($url, [
                    'business_id' => $businessId,
                    'terminal_id' => $terminal->terminal_id
                ]);

            if (!$response->successful()) {
                Log::error('EIS regenerate credentials API error', [
                    'business_id' => $businessId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to regenerate credentials: ' . $response->body()
                ];
            }

            $responseData = $response->json();
            
            if (isset($responseData['data']['jwtToken']) && isset($responseData['data']['secretKey'])) {
                $terminal->update([
                    'jwt_token' => $responseData['data']['jwtToken'],
                    'secret_key' => $responseData['data']['secretKey']
                ]);
                
                // Update eis_settings with new secret_key
                $setting = EisSetting::where('business_id', $businessId)->first();
                if ($setting) {
                    $setting->update([
                        'secret_key' => $responseData['data']['secretKey']
                    ]);
                } else {
                    // Create new setting if not exists
                    EisSetting::create([
                        'business_id' => $businessId,
                        'device_id' => $terminal->terminal_id,
                        'secret_key' => $responseData['data']['secretKey'],
                        'tpin' => $terminal->taxpayer_id ?? null,
                        'status' => true,
                        'sync_status' => 'success',
                        'last_sync_at' => now(),
                        'successful_syncs' => 1,
                        'failed_syncs' => 0,
                    ]);
                }
            }

            Log::info('Terminal credentials regenerated successfully', [
                'business_id' => $businessId,
                'terminal_config_id' => $terminal->id
            ]);

            return [
                'success' => true,
                'message' => 'Terminal credentials regenerated successfully',
                'data' => [
                    'jwt_token' => $terminal->jwt_token,
                    'secret_key' => $terminal->secret_key
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Failed to regenerate credentials', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to regenerate credentials: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sync terminal configuration.
     *
     * @param int $businessId
     * @param string $token
     * @return array
     */
    public function syncTerminal(int $businessId, string $token): array
    {
        try {
            Log::info('Syncing terminal configuration', [
                'business_id' => $businessId
            ]);

            $configuration = $this->syncService->sync($businessId, $token);
            
            $terminal = EisTerminalConfiguration::where('configuration_id', $configuration->id)
                ->with(['terminalSite', 'offlineLimit'])
                ->first();

            return [
                'success' => true,
                'message' => 'Terminal synced successfully',
                'data' => $terminal ? $this->getTerminalDetails($terminal) : null
            ];

        } catch (\Exception $e) {
            Log::error('Failed to sync terminal', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to sync terminal: ' . $e->getMessage()
            ];
        }
    }
}