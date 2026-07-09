<?php

namespace App\Jobs;

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
                        ->orWhere('last_sync_at', '<', now()->subMinutes(5));
                })
                ->where(function ($query) {
                    $query->whereNull('sync_status')
                        ->orWhere('sync_status', '!=', 'failed')
                        ->orWhere('sync_error_retry_after', '<', now());
                })
                ->orderBy('last_sync_at', 'asc')
                ->limit(500);
            
            $settingsQuery->chunkById(50, function ($chunk) use ($syncService, &$processedCount, &$successCount, &$failureCount, &$skippedCount) {
                foreach ($chunk as $setting) {
                    $processedCount++;
                    
                    if ($this->shouldSkipSync($setting)) {
                        $skippedCount++;
                        continue;
                    }
                    
                    $this->processSetting($setting, $syncService, $successCount, $failureCount);
                }
                
                if ($chunk->count() > 0) {
                    usleep(100000);
                }
            });
            
        } catch (\Throwable $e) {
            Log::error('EIS Configuration Sync Job - Critical Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        $executionTime = microtime(true) - $startTime;
        
        Log::info('EIS Configuration Sync Job - Completed', [
            'processed' => $processedCount,
            'successful' => $successCount,
            'failed' => $failureCount,
            'skipped' => $skippedCount,
            'execution_time' => round($executionTime, 2)
        ]);
        
        $this->updateMetrics($processedCount, $successCount, $failureCount, $executionTime);
    }

    protected function shouldSkipSync($setting): bool
    {
        if ($setting->last_sync_at && $setting->last_sync_at->diffInMinutes(now()) < 1) {
            return true;
        }
        
        if ($setting->sync_status === 'failed' && 
            $setting->updated_at && 
            $setting->updated_at->diffInMinutes(now()) < 15) {
            return true;
        }
        
        $rateLimitKey = 'eis_sync_rate_limit';
        $rateLimitCount = Cache::get($rateLimitKey, 0);
        
        if ($rateLimitCount >= 30) {
            Log::warning('EIS Sync rate limit reached', ['count' => $rateLimitCount]);
            return true;
        }
        
        Cache::increment($rateLimitKey);
        Cache::expire($rateLimitKey, 60);
        
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
            
            $setting->update([
                'sync_status' => 'failed',
                'sync_error' => $e->getMessage(),
                'sync_error_retry_after' => now()->addMinutes(5),
                'failed_syncs' => ($setting->failed_syncs ?? 0) + 1,
                'last_sync_attempt' => now(),
            ]);
            
            Log::error('EIS Sync - Individual Failure', [
                'business_id' => $setting->business_id,
                'error' => $e->getMessage()
            ]);
        }
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
}