<?php

namespace App\EIS\Services\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\EIS\Exceptions\EisException;
use App\EIS\Services\Authentication\AuthenticationService;

class HttpClientService
{
    protected string $baseUrl;
    protected int $timeout;
    protected AuthenticationService $auth;

    public function __construct(AuthenticationService $auth)
    {
        $this->baseUrl = rtrim(config('eis.base_url'), '/');
        $this->timeout = config('eis.timeout', 30);
        $this->auth = $auth;
    }

    public function get(string $endpoint, array $query = [], array $headers = [])
    {
        return $this->request('GET', $endpoint, $query, $headers);
    }

    public function post(string $endpoint, array $data = [], array $headers = [])
    {
        return $this->request('POST', $endpoint, $data, $headers);
    }

    public function put(string $endpoint, array $data = [], array $headers = [])
    {
        return $this->request('PUT', $endpoint, $data, $headers);
    }

    public function delete(string $endpoint, array $headers = [])
    {
        return $this->request('DELETE', $endpoint, [], $headers);
    }

    private function request(string $method, string $endpoint, array $payload = [], array $headers = [])
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = Http::timeout($this->timeout)
                ->retry(
                    config('eis.retry.times', 2),
                    config('eis.retry.sleep', 100)
                )
                ->withToken($this->auth->getAccessToken())
                ->withHeaders(array_merge([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ], $headers))
                ->send($method, $url, $this->buildOptions($method, $payload));

            if (!$response->successful()) {
                Log::error('EIS API Request Failed', [
                    'url' => $url,
                    'method' => $method,
                    'payload' => $payload,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new EisException(
                    "EIS API error: " . $response->body(),
                    $response->status()
                );
            }

            return $response->json();

        } catch (\Throwable $e) {
            Log::error('EIS Client Exception', [
                'message' => $e->getMessage(),
                'endpoint' => $endpoint,
                'method' => $method,
            ]);

            throw new EisException($e->getMessage(), $e->getCode(), [
                'endpoint' => $endpoint
            ]);
        }
    }

    private function buildUrl(string $endpoint): string
    {
        return $this->baseUrl
            . '/api/'
            . config('eis.version')
            . '/'
            . ltrim($endpoint, '/');
    }

    private function buildOptions(string $method, array $payload): array
    {
        return match ($method) {
            'GET' => ['query' => $payload],
            default => ['json' => $payload],
        };
    }
}