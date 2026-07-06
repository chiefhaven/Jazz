<?php

namespace Modules\EIS\Services\Http;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\EIS\Exceptions\EisSaleException;

class EisSaleClient
{
    protected string $baseUrl;

    protected int $timeout;

    protected int $connectTimeout;

    protected int $retries;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('eis.base_url'), '/');

        $this->timeout = config('eis.http_timeout', 60);
        $this->connectTimeout = config('eis.connect_timeout', 10);
        $this->retries = config('eis.http_retries', 2);

    }

    /**
     * Submit sale transaction to EIS.
     *
     * @throws EisSaleException
     */
    public function submit(array $payload, object $settings): array
    {
        Log::info('Submitting sale transaction to EIS', [
            'payload' => $payload,
            'settings' => $settings,
        ]);

        if (empty($settings->jwt_token)) {
            throw new EisSaleException('Missing EIS JWT token.');
        }

        if (empty($settings->secret_key)) {
            throw new EisSaleException('Missing EIS Secret Key.');
        }

        if (empty($settings->branch_id)) {
            throw new EisSaleException('Missing EIS Site ID.');
        }

        if (empty($settings->tpin)) {
            throw new EisSaleException('Missing EIS TIN.');
        }

        $url = $this->baseUrl . '/sales/submit-sales-transaction';

        try {

            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout($this->connectTimeout)
                ->timeout($this->timeout)
                ->retry(
                    $this->retries,
                    2000,
                    function ($exception) {
                        return $exception instanceof ConnectionException;
                    }
                )
                ->withToken($settings->jwt_token)
                ->withHeaders([
                    'X-Secret-Key' => $settings->secret_key,
                ])
                ->post($url, $payload);

            if ($response->failed()) {

                Log::error('EIS request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                throw new EisSaleException(
                    sprintf(
                        'EIS returned HTTP %s: %s',
                        $response->status(),
                        $response->body()
                    ),
                    $response->status()
                );
            }

            Log::info('EIS invoice submitted successfully.', [
                'url' => $url,
                'invoice' => $payload['invoiceHeader']['invoiceNumber'] ?? null,
            ]);

            return $response->json();

        } catch (ConnectionException $e) {

            Log::error('Unable to connect to EIS server.', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

        } catch (RequestException $e) {

            Log::error('EIS request exception.', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw new EisSaleException(
                $e->getMessage(),
                0,
                $e
            );

        } catch (\Throwable $e) {

            Log::error('Unexpected EIS client error.', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

        }
    }
}