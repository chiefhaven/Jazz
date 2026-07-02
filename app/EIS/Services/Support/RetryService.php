<?php

namespace App\EIS\Services\Support;

class RetryService
{
    /**
     * Calculate exponential backoff delay
     */
    public function delay(int $attempt): int
    {
        return match ($attempt) {
            1 => 60,        // 1 min
            2 => 120,       // 2 min
            3 => 300,       // 5 min
            default => 600, // 10 min
        };
    }

    /**
     * Check if max retries reached
     */
    public function canRetry(int $attempts, int $max = 3): bool
    {
        return $attempts < $max;
    }
}