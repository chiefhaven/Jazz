<?php

namespace Modules\EIS\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\TerminalConfiguration;
use Modules\EIS\Models\EisConfiguration;
use Modules\EIS\Services\Terminal\EisTerminalActivationService;

class TerminalActivationController extends Controller
{
    public function __construct(
        protected EisTerminalActivationService $activationService
    ) {
    }

    /**
     * Activate terminal with environment details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activateTerminal(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:eis_configurations,business_id',
            'token' => 'required|string',
            'environment' => 'sometimes|array',
            'environment.platform' => 'sometimes|array',
            'environment.platform.osName' => 'sometimes|string|max:255',
            'environment.platform.osVersion' => 'sometimes|string|max:255',
            'environment.platform.osBuild' => 'sometimes|string|max:255',
            'environment.platform.macAddress' => 'sometimes|string|max:255',
            'environment.pos' => 'sometimes|array',
            'environment.pos.productID' => 'sometimes|string|max:255',
            'environment.pos.productVersion' => 'sometimes|string|max:255'
        ]);

        $activatedBy = auth()->id() ?? null;
        $environment = $request->input('environment', []);

        $result = $this->activationService->activateTerminal(
            $request->business_id,
            $request->token,
            $environment,
            $activatedBy
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Deactivate terminal.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:eis_configurations,business_id',
            'reason' => 'nullable|string|max:500'
        ]);

        $deactivatedBy = auth()->id() ?? null;

        $result = $this->activationService->deactivate(
            $request->business_id,
            $request->reason,
            $deactivatedBy
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Toggle terminal activation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:eis_configurations,business_id',
            'token' => 'required|string'
        ]);

        $toggledBy = auth()->id() ?? null;

        $result = $this->activationService->toggle(
            $request->business_id,
            $request->token,
            $toggledBy
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get terminal status.
     *
     * @param int $businessId
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(int $businessId)
    {
        // Validate business exists
        if (!EisConfiguration::where('business_id', $businessId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Business configuration not found'
            ], 404);
        }

        $result = $this->activationService->getStatus($businessId);

        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Check if terminal is active.
     *
     * @param int $businessId
     * @return \Illuminate\Http\JsonResponse
     */
    public function isActive(int $businessId)
    {
        // Validate business exists
        if (!EisConfiguration::where('business_id', $businessId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Business configuration not found',
                'is_active' => false
            ], 404);
        }

        $isActive = $this->activationService->isActive($businessId);

        return response()->json([
            'success' => true,
            'business_id' => $businessId,
            'is_active' => $isActive
        ]);
    }

    /**
     * Get terminal activation history.
     *
     * @param int $businessId
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(int $businessId)
    {
        // Validate business exists
        if (!EisConfiguration::where('business_id', $businessId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Business configuration not found'
            ], 404);
        }

        $result = $this->activationService->getActivationHistory($businessId);

        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Get terminal credentials.
     *
     * @param int $businessId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCredentials(int $businessId)
    {
        try {
            $configuration = EisConfiguration::where('business_id', $businessId)->first();
            
            if (!$configuration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuration not found'
                ], 404);
            }

            $terminal = TerminalConfiguration::where('configuration_id', $configuration->id)->first();
            
            if (!$terminal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terminal configuration not found'
                ], 404);
            }

            if (!$terminal->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terminal is not active'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'jwt_token' => $terminal->jwt_token,
                    'secret_key' => $terminal->secret_key,
                    'has_credentials' => $terminal->hasCredentials()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get terminal credentials', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get credentials: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerate terminal credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function regenerateCredentials(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:eis_configurations,business_id',
            'token' => 'required|string'
        ]);

        try {
            $configuration = EisConfiguration::where('business_id', $request->business_id)->first();
            
            if (!$configuration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuration not found'
                ], 404);
            }

            $terminal = TerminalConfiguration::where('configuration_id', $configuration->id)->first();
            
            if (!$terminal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terminal configuration not found'
                ], 404);
            }

            // Call API to regenerate credentials
            // This would be implemented in the service
            $result = $this->activationService->regenerateCredentials(
                $request->business_id,
                $request->token
            );

            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('Failed to regenerate credentials', [
                'business_id' => $request->business_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate credentials: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk activate terminals.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkActivate(Request $request)
    {
        $request->validate([
            'business_ids' => 'required|array',
            'business_ids.*' => 'integer|exists:eis_configurations,business_id',
            'token' => 'required|string',
            'environment' => 'sometimes|array'
        ]);

        $activatedBy = auth()->id() ?? null;
        $environment = $request->input('environment', []);
        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($request->business_ids as $businessId) {
            $result = $this->activationService->activateTerminal(
                $businessId,
                $request->token,
                $environment,
                $activatedBy
            );

            $results[$businessId] = $result;
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk activation completed: {$successCount} succeeded, {$failureCount} failed",
            'data' => $results,
            'summary' => [
                'total' => count($request->business_ids),
                'successful' => $successCount,
                'failed' => $failureCount
            ]
        ]);
    }

    /**
     * Bulk deactivate terminals.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDeactivate(Request $request)
    {
        $request->validate([
            'business_ids' => 'required|array',
            'business_ids.*' => 'integer|exists:eis_configurations,business_id',
            'reason' => 'nullable|string|max:500'
        ]);

        $deactivatedBy = auth()->id() ?? null;
        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($request->business_ids as $businessId) {
            $result = $this->activationService->deactivate(
                $businessId,
                $request->reason,
                $deactivatedBy
            );

            $results[$businessId] = $result;
            
            if ($result['success']) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk deactivation completed: {$successCount} succeeded, {$failureCount} failed",
            'data' => $results,
            'summary' => [
                'total' => count($request->business_ids),
                'successful' => $successCount,
                'failed' => $failureCount
            ]
        ]);
    }

    /**
     * Get terminal details.
     *
     * @param int $businessId
     * @return \Illuminate\Http\JsonResponse
     */
    public function details(int $businessId)
    {
        try {
            $configuration = EisConfiguration::where('business_id', $businessId)->first();
            
            if (!$configuration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuration not found'
                ], 404);
            }

            $terminal = TerminalConfiguration::where('configuration_id', $configuration->id)
                ->with(['terminalSite', 'offlineLimit'])
                ->first();

            if (!$terminal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terminal configuration not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'terminal' => [
                        'id' => $terminal->id,
                        'terminal_label' => $terminal->terminal_label,
                        'is_active' => $terminal->is_active,
                        'status' => $terminal->status,
                        'display_name' => $terminal->display_name,
                        'trading_name' => $terminal->trading_name,
                        'email_address' => $terminal->email_address,
                        'phone_number' => $terminal->phone_number,
                        'address' => $terminal->full_address,
                        'version' => $terminal->version,
                        'terminal_id' => $terminal->terminal_id,
                        'terminal_position' => $terminal->terminal_position,
                        'taxpayer_id' => $terminal->taxpayer_id,
                        'activation_date' => $terminal->activation_date,
                        'activated_at' => $terminal->activated_at,
                        'deactivated_at' => $terminal->deactivated_at,
                        'activation_duration' => $terminal->activation_duration,
                        'has_credentials' => $terminal->hasCredentials(),
                        'last_synced_at' => $terminal->last_synced_at
                    ],
                    'site' => $terminal->terminalSite ? [
                        'site_id' => $terminal->terminalSite->site_id,
                        'site_name' => $terminal->terminalSite->site_name
                    ] : null,
                    'offline_limit' => $terminal->offlineLimit ? [
                        'max_transaction_age_hours' => $terminal->offlineLimit->max_transaction_age_hours,
                        'max_cumulative_amount' => $terminal->offlineLimit->max_cumulative_amount,
                        'formatted_max_amount' => $terminal->offlineLimit->formatted_max_amount
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get terminal details', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get terminal details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync terminal configuration.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncTerminal(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:eis_configurations,business_id',
            'token' => 'required|string'
        ]);

        try {
            $result = $this->activationService->syncTerminal(
                $request->business_id,
                $request->token
            );

            return response()->json($result, $result['success'] ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('Failed to sync terminal', [
                'business_id' => $request->business_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync terminal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get terminal health status.
     *
     * @param int $businessId
     * @return \Illuminate\Http\JsonResponse
     */
    public function health(int $businessId)
    {
        try {
            $configuration = EisConfiguration::where('business_id', $businessId)->first();
            
            if (!$configuration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuration not found',
                    'health_score' => 0
                ], 404);
            }

            $terminal = TerminalConfiguration::where('configuration_id', $configuration->id)->first();
            
            if (!$terminal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terminal configuration not found',
                    'health_score' => 0
                ], 404);
            }

            // Calculate health score
            $healthScore = 100;
            $issues = [];

            // Check if terminal is active
            if (!$terminal->is_active) {
                $healthScore -= 30;
                $issues[] = 'Terminal is inactive';
            }

            // Check if terminal has credentials
            if (!$terminal->hasCredentials()) {
                $healthScore -= 20;
                $issues[] = 'Missing terminal credentials';
            }

            // Check if terminal has site
            if (!$terminal->hasSite()) {
                $healthScore -= 10;
                $issues[] = 'No site configured';
            }

            // Check if terminal has offline limit
            if (!$terminal->hasOfflineLimit()) {
                $healthScore -= 10;
                $issues[] = 'No offline limit configured';
            }

            // Check if terminal was synced recently (within 24 hours)
            if ($terminal->last_synced_at && $terminal->last_synced_at->diffInHours(now()) > 24) {
                $healthScore -= 15;
                $issues[] = 'Terminal not synced in last 24 hours';
            }

            // Check activation age
            if ($terminal->activation_date && $terminal->activation_date->diffInMonths(now()) > 6) {
                $healthScore -= 5;
                $issues[] = 'Terminal activation is older than 6 months';
            }

            return response()->json([
                'success' => true,
                'business_id' => $businessId,
                'health_score' => max(0, $healthScore),
                'status' => $healthScore >= 70 ? 'healthy' : ($healthScore >= 40 ? 'warning' : 'critical'),
                'issues' => $issues,
                'data' => [
                    'is_active' => $terminal->is_active,
                    'has_credentials' => $terminal->hasCredentials(),
                    'has_site' => $terminal->hasSite(),
                    'has_offline_limit' => $terminal->hasOfflineLimit(),
                    'last_synced_at' => $terminal->last_synced_at,
                    'activation_days' => $terminal->activation_duration,
                    'version' => $terminal->version
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get terminal health', [
                'business_id' => $businessId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get terminal health: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook for terminal activation callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activationCallback(Request $request)
    {
        try {
            Log::info('Terminal activation callback received', [
                'payload' => $request->all()
            ]);

            // Verify webhook signature if needed
            // $this->verifyWebhookSignature($request);

            $businessId = $request->input('business_id');
            $activationCode = $request->input('activation_code');
            $status = $request->input('status');
            $data = $request->input('data', []);

            if (!$businessId || !$activationCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields'
                ], 400);
            }

            // Process the callback
            $result = $this->activationService->handleActivationCallback(
                $businessId,
                $activationCode,
                $status,
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Callback processed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Activation callback failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process callback: ' . $e->getMessage()
            ], 500);
        }
    }
}