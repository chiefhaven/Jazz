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
     * Activate terminal via EIS API.
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
            Log::info('Terminal activation API request started', [
                'business_id' => $businessId,
                'activation_code' => $activationCode,
                'environment' => $environment
            ]);

            // Call EIS API to activate terminal
            $activationResponse = $this->callActivationAPI($businessId, $activationCode, $environment);

            // Process the activation response
            return $this->processActivationResponse(
                $businessId,
                $activationResponse,
                $activationCode,
                $environment,
                $activatedBy
            );

        } catch (\Exception $e) {
            Log::error('Terminal activation API failed', [
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
     * Call EIS activation API with correct payload structure.
     *
     * @param int $businessId
     * @param string $activationCode
     * @param array $environment
     * @return object
     * @throws \Exception
     */
    private function callActivationAPI(int $businessId, string $activationCode, array $environment): object
    {
        $url = $this->apiBaseUrl . '/api/v1/onboarding/activate-terminal';

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

        // Check if response has error (statusCode: 0 = success, other = error)
        if (isset($responseData->statusCode) && $responseData->statusCode !== 0) {
            $errorMessage = $responseData->remark ?? 'Unknown error';
            
            if (!empty($responseData->errors)) {
                $errors = [];
                foreach ($responseData->errors as $error) {
                    $errors[] = $error->errorMessage ?? 'Unknown error';
                }
                $errorMessage .= ': ' . implode(', ', $errors);
            }

            throw new \Exception('EIS activation API returned error: ' . $errorMessage, $responseData->statusCode);
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
                    throw new \Exception('No activated terminal data in response');
                }

                if (!$configurationData) {
                    throw new \Exception('No configuration data in response');
                }

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

                Log::info('Terminal activated successfully via API', [
                    'business_id' => $businessId,
                    'terminal_config_id' => $terminal->id,
                    'terminal_id' => $activatedTerminal->terminalId ?? null,
                    'activation_date' => $activatedTerminal->activationDate ?? null,
                    'activated_by' => $activatedBy
                ]);

                return [
                    'success' => true,
                    'message' => 'Terminal activated successfully',
                    'data' => $this->getTerminalDetails($terminal, $activatedTerminal),
                    'terminal_credentials' => $activatedTerminal->terminalCredentials ?? null,
                    'activation_code' => $activationCode,
                    'status_code' => $response->statusCode ?? 0
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
            'activation_environment' => $terminal->activation_environment ? 
                json_decode($terminal->activation_environment, true) : null,
            'terminal_id' => $terminal->terminal_id,
            'terminal_position' => $terminal->terminal_position,
            'taxpayer_id' => $terminal->taxpayer_id,
            'activation_date' => $terminal->activation_date,
            'site' => $terminal->terminalSite ? [
                'site_id' => $terminal->terminalSite->site_id,
                'site_name' => $terminal->terminalSite->site_name
            ] : null,
            'offline_limit' => $terminal->offlineLimit ? [
                'max_transaction_age_hours' => $terminal->offlineLimit->max_transaction_age_hours,
                'max_cumulative_amount' => $terminal->offlineLimit->max_cumulative_amount
            ] : null
        ];

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
                'reason' => $reason,
                'deactivated_by' => $deactivatedBy
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
                    'message' => 'Terminal configuration not found for this business'
                ];
            }

            if (!$terminal->is_active) {
                return [
                    'success' => true,
                    'message' => 'Terminal is already inactive',
                    'data' => $this->getTerminalDetails($terminal)
                ];
            }

            // Call deactivation API if needed
            $this->callDeactivationAPI($businessId, $terminal->activation_code ?? null);

            // Deactivate the terminal
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
                'terminal_config_id' => $terminal->id,
                'deactivated_at' => now(),
                'deactivated_by' => $deactivatedBy,
                'reason' => $reason
            ]);

            return [
                'success' => true,
                'message' => 'Terminal deactivated successfully',
                'data' => $this->getTerminalDetails($terminal)
            ];

        } catch (\Exception $e) {
            Log::error('Terminal deactivation failed', [
                'business_id' => $businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to deactivate terminal: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Call deactivation API.
     *
     * @param int $businessId
     * @param string|null $activationCode
     * @return void
     */
    private function callDeactivationAPI(int $businessId, ?string $activationCode): void
    {
        if (!$activationCode) {
            return;
        }

        try {
            $url = $this->apiBaseUrl . '/api/v1/onboarding/deactivate-terminal';

            $response = Http::acceptJson()
                ->timeout(30)
                ->post($url, [
                    'business_id' => $businessId,
                    'activation_code' => $activationCode,
                    'deactivated_at' => now()->toISOString()
                ]);

            if (!$response->successful()) {
                Log::warning('EIS deactivation API warning', [
                    'business_id' => $businessId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::warning('EIS deactivation API error', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);
            // Don't throw - deactivation can continue even if API fails
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
    public function toggle(int $businessId, ?int $toggledBy = null): array
    {
        try {
            Log::info('Terminal activation toggle requested', [
                'business_id' => $businessId,
                'toggled_by' => $toggledBy
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
                'new_status' => $newStatus ? 'active' : 'inactive',
                'toggled_by' => $toggledBy
            ]);

            return [
                'success' => true,
                'message' => 'Terminal ' . ($newStatus ? 'activated' : 'deactivated') . ' successfully',
                'data' => $this->getTerminalDetails($terminal)
            ];

        } catch (\Exception $e) {
            Log::error('Terminal toggle failed', [
                'business_id' => $businessId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to toggle terminal: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get terminal status for a business.
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
                    'message' => 'Configuration not found for this business',
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

            $history = [
                'activated_at' => $terminal->activated_at,
                'activated_by' => $terminal->activated_by,
                'deactivated_at' => $terminal->deactivated_at,
                'deactivated_by' => $terminal->deactivated_by,
                'deactivation_reason' => $terminal->deactivation_reason,
                'toggled_at' => $terminal->toggled_at,
                'toggled_by' => $terminal->toggled_by,
                'current_status' => $terminal->is_active ? 'Active' : 'Inactive',
                'activation_code' => $terminal->activation_code,
                'activation_environment' => $terminal->activation_environment ? 
                    json_decode($terminal->activation_environment, true) : null,
                'terminal_id' => $terminal->terminal_id,
                'terminal_position' => $terminal->terminal_position,
                'activation_date' => $terminal->activation_date
            ];

            return [
                'success' => true,
                'data' => $history
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

            // Call API to regenerate credentials
            $url = $this->apiBaseUrl . '/api/v1/configuration/request-new-terminal-token';
            
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
            
            // Update terminal with new credentials
            if (isset($responseData['data']['jwtToken']) && isset($responseData['data']['secretKey'])) {
                $terminal->update([
                    'jwt_token' => $responseData['data']['jwtToken'],
                    'secret_key' => $responseData['data']['secretKey']
                ]);
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

    /**
     * Handle activation callback.
     *
     * @param int $businessId
     * @param string $activationCode
     * @param string $status
     * @param array $data
     * @return array
     */
    public function handleActivationCallback(int $businessId, string $activationCode, string $status, array $data): array
    {
        try {
            Log::info('Processing activation callback', [
                'business_id' => $businessId,
                'activation_code' => $activationCode,
                'status' => $status
            ]);

            $terminal = EisTerminalConfiguration::where('activation_code', $activationCode)
                ->whereHas('configuration', function ($query) use ($businessId) {
                    $query->where('business_id', $businessId);
                })
                ->first();

            if (!$terminal) {
                return [
                    'success' => false,
                    'message' => 'Terminal not found with activation code'
                ];
            }

            if ($status === 'activated') {
                $terminal->update([
                    'is_active' => true,
                    'activated_at' => now(),
                    'activation_date' => $data['activation_date'] ?? now(),
                    'jwt_token' => $data['jwt_token'] ?? null,
                    'secret_key' => $data['secret_key'] ?? null,
                    'terminal_id' => $data['terminal_id'] ?? null
                ]);
            } elseif ($status === 'deactivated') {
                $terminal->update([
                    'is_active' => false,
                    'deactivated_at' => now(),
                    'deactivation_reason' => $data['reason'] ?? 'Deactivated via callback'
                ]);
            }

            Log::info('Activation callback processed successfully', [
                'business_id' => $businessId,
                'terminal_id' => $terminal->id,
                'status' => $status
            ]);

            return [
                'success' => true,
                'message' => 'Callback processed successfully',
                'terminal_id' => $terminal->id,
                'status' => $status
            ];

        } catch (\Exception $e) {
            Log::error('Failed to process activation callback', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process callback: ' . $e->getMessage()
            ];
        }
    }
}