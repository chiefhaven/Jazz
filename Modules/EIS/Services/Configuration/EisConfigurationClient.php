<?php

namespace Modules\EIS\Services\Configuration;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Services\Configuration\EISConfigurationResponse;

class EisConfigurationClient
{
    private const MAX_RETRIES = 3;
    private const TIMEOUT_SECONDS = 60;
    private const INITIAL_BACKOFF_SECONDS = 1;

    /**
     * Fetch the latest configurations from the EIS API with retry logic.
     *
     * @param string $token
     * @return EISConfigurationResponse
     * @throws \Exception
     */
    public function latest(string $token): EISConfigurationResponse
    {
        $this->validateToken($token);

        $attempt = 0;
        $lastException = null;

        do {
            try {
                $response = $this->makeApiRequest($token);
                $configResponse = new EISConfigurationResponse($response->object());
                
                // Log successful response
                Log::info('EIS Configuration Client - Success', [
                    'status_code' => $configResponse->getStatusCode(),
                    'remark' => $configResponse->getRemark()
                ]);
                
                return $configResponse;
            } catch (\Exception $e) {
                $attempt++;
                $lastException = $e;
                
                Log::warning('EIS API attempt failed', [
                    'attempt' => $attempt,
                    'max_retries' => self::MAX_RETRIES,
                    'error' => $e->getMessage()
                ]);
                
                if ($attempt < self::MAX_RETRIES) {
                    $this->exponentialBackoff($attempt);
                }
            }
        } while ($attempt < self::MAX_RETRIES);

        // All retries failed
        Log::error('EIS Configuration Client - All retries failed', [
            'attempts' => $attempt,
            'error' => $lastException ? $lastException->getMessage() : 'Unknown error'
        ]);

        throw new \Exception(
            'EIS Configuration API request failed after ' . self::MAX_RETRIES . ' attempts'
        );
    }

    /**
     * Make the API request to EIS.
     *
     * @param string $token
     * @return \Illuminate\Http\Client\Response
     * @throws \Exception
     */
    private function makeApiRequest(string $token): \Illuminate\Http\Client\Response
    {
        $baseUrl = config('eis.base_url');
        
        if (empty($baseUrl)) {
            throw new \Exception('EIS base URL is not configured');
        }

        $url = $baseUrl . '/configuration/get-latest-configs';

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(self::TIMEOUT_SECONDS)
            ->post($url);

        if (!$response->successful()) {
            throw new \Exception(
                sprintf(
                    'API request failed with status %d: %s',
                    $response->status(),
                    substr($response->body(), 0, 500)
                )
            );
        }

        return $response;
    }

    /**
     * Validate the token format.
     *
     * @param string $token
     * @throws \InvalidArgumentException
     */
    private function validateToken(string $token): void
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('Authentication token is empty');
        }

        // Basic token validation - adjust as needed
        if (strlen($token) < 20) {
            throw new \InvalidArgumentException('Authentication token appears invalid (too short)');
        }
    }

    /**
     * Implement exponential backoff for retry logic.
     *
     * @param int $attempt
     * @return void
     */
    private function exponentialBackoff(int $attempt): void
    {
        $backoffSeconds = self::INITIAL_BACKOFF_SECONDS * pow(2, $attempt - 1);
        sleep($backoffSeconds);
    }
}