<?php

namespace Modules\EIS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Models\EisSetting;
use Modules\EIS\Services\Configuration\ConfigurationSyncService;

class SyncEISConfigurationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;
    
    // Rate limiting constants
    private const RATE_LIMIT_KEY = 'eis_sync_rate_limit';
    private const RATE_LIMIT_TTL = 60; // 60 seconds
    private const RATE_LIMIT_MAX = 30; // Max 30 requests per minute
    private const MIN_SYNC_INTERVAL_MINUTES = 1;
    private const RETRY_BACKOFF_MINUTES = 15;
    private const CHUNK_SIZE = 50;
    private const MAX_PROCESS_LIMIT = 500;

    public function handle(ConfigurationSyncService $syncService): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $successCount = 0;
        $failureCount = 0;
        $skippedCount = 0;
        
        Log::info('EIS Configuration Sync Job - Started');
        
        try {
            $settingsQuery = EisSetting::where('status', true)
                ->whereNotNull('jwt_token')
                ->where(function ($query) {
                    $query->whereNull('last_sync_at')
                        ->orWhere('last_sync_at', '<', now()->subMinutes(self::MIN_SYNC_INTERVAL_MINUTES));
                })
                ->where(function ($query) {
                    $query->whereNull('sync_status')
                        ->orWhere('sync_status', '!=', 'failed')
                        ->orWhere('sync_error_retry_after', '<', now());
                })
                ->orderBy('last_sync_at', 'asc')
                ->limit(self::MAX_PROCESS_LIMIT);
            
            $settingsQuery->chunkById(self::CHUNK_SIZE, function ($chunk) use ($syncService, &$processedCount, &$successCount, &$failureCount, &$skippedCount) {
                foreach ($chunk as $setting) {
                    $processedCount++;
                    
                    if ($this->shouldSkipSync($setting)) {
                        $skippedCount++;
                        continue;
                    }
                    
                    $this->processSetting($setting, $syncService, $successCount, $failureCount);
                }
                
                if ($chunk->count() > 0) {
                    usleep(100000); // 100ms delay between chunks
                }
            });
            
        } catch (\Throwable $e) {
            Log::error('EIS Configuration Sync Job - Critical Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->sendAlert('EIS Sync Critical Error', $e->getMessage());
        }
        
        $executionTime = microtime(true) - $startTime;
        
        Log::info('EIS Configuration Sync Job - Completed', [
            'processed' => $processedCount,
            'successful' => $successCount,
            'failed' => $failureCount,
            'skipped' => $skippedCount,
            'execution_time' => round($executionTime, 2),
            'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024, 2)
        ]);
        
        $this->updateMetrics($processedCount, $successCount, $failureCount, $executionTime);
    }

    protected function shouldSkipSync($setting): bool
    {
        // Skip if synced recently
        if ($setting->last_sync_at && $setting->last_sync_at->diffInMinutes(now()) < self::MIN_SYNC_INTERVAL_MINUTES) {
            return true;
        }
        
        // Skip if failed recently (backoff period)
        if ($setting->sync_status === 'failed' && 
            $setting->updated_at && 
            $setting->updated_at->diffInMinutes(now()) < self::RETRY_BACKOFF_MINUTES) {
            return true;
        }
        
        // Global rate limiting
        $rateLimitCount = Cache::get(self::RATE_LIMIT_KEY, 0);
        
        if ($rateLimitCount >= self::RATE_LIMIT_MAX) {
            Log::warning('EIS Sync rate limit reached', [
                'count' => $rateLimitCount,
                'max' => self::RATE_LIMIT_MAX
            ]);
            return true;
        }
        
        // Increment rate limit counter with TTL
        Cache::put(self::RATE_LIMIT_KEY, $rateLimitCount + 1, self::RATE_LIMIT_TTL);
        
        return false;
    }

    protected function processSetting($setting, $syncService, &$successCount, &$failureCount): void
    {
        try {
            DB::transaction(function () use ($setting, $syncService, &$successCount) {
                $result = $syncService->sync(
                    $setting->business_id,
                    $setting->jwt_token
                );
                
                $setting->update([
                    'last_sync_at' => now(),
                    'sync_status' => 'success',
                    'sync_error' => null,
                    'sync_error_retry_after' => null,
                    'successful_syncs' => ($setting->successful_syncs ?? 0) + 1,
                    'global_version' => $result->global_version ?? null,
                    'terminal_version' => $result->terminal_version ?? null,
                    'taxpayer_version' => $result->taxpayer_version ?? null,
                ]);
                
                $successCount++;
            });
            
        } catch (\Throwable $e) {
            $failureCount++;
            
            // Determine if error is retryable
            $isRetryable = $this->isRetryableError($e);
            $retryAfter = $isRetryable ? now()->addMinutes(5) : null;
            
            $setting->update([
                'sync_status' => 'failed',
                'sync_error' => $e->getMessage(),
                'sync_error_retry_after' => $retryAfter,
                'failed_syncs' => ($setting->failed_syncs ?? 0) + 1,
                'last_sync_attempt' => now(),
            ]);
            
            Log::error('EIS Sync - Individual Failure', [
                'business_id' => $setting->business_id,
                'error' => $e->getMessage(),
                'is_retryable' => $isRetryable
            ]);
            
            // Alert on repeated failures
            if (($setting->failed_syncs ?? 0) >= 5) {
                $this->sendAlert(
                    'Repeated EIS Sync Failures',
                    "Business ID {$setting->business_id} has failed {$setting->failed_syncs} times",
                    ['business_id' => $setting->business_id]
                );
            }
        }
    }

    protected function isRetryableError(\Throwable $e): bool
    {
        $nonRetryableErrors = [
            'Invalid token',
            'Authentication failed',
            'Business not found',
            'Validation error',
            'Invalid configuration',
            'TIN not found',
            'Taxpayer not found'
        ];
        
        $message = strtolower($e->getMessage());
        
        foreach ($nonRetryableErrors as $error) {
            if (stripos($message, strtolower($error)) !== false) {
                return false;
            }
        }
        
        return true;
    }

    protected function updateMetrics(int $processed, int $success, int $failure, float $executionTime): void
    {
        $metrics = [
            'last_run' => now(),
            'processed' => $processed,
            'successful' => $success,
            'failed' => $failure,
            'execution_time' => $executionTime,
            'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2)
        ];
        
        Cache::put('eis_sync_metrics_last', $metrics, now()->addDay());
    }
    
    protected function sendAlert(string $subject, string $message, array $context = []): void
    {
        Log::warning('EIS Alert', [
            'subject' => $subject,
            'message' => $message,
            'context' => $context
        ]);
        
        // Uncomment when ready to implement actual alerting
        // Notification::route('slack', config('services.slack.webhook_url'))
        //     ->notify(new EISAlertNotification($subject, $message, $context));
    }
}