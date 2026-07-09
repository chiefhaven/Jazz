<?php

namespace Modules\EIS\Services\Configuration\Responses;

class EISConfigurationResponse
{
    private int $statusCode;
    private string $remark;
    private object $data;
    private array $errors;
    private ?array $rawResponse;

    public function __construct(object $response)
    {
        $this->statusCode = $response->statusCode ?? 1;
        $this->remark = $response->remark ?? '';
        $this->data = $response->data ?? (object) [];
        $this->errors = $response->errors ?? [];
        $this->rawResponse = json_decode(json_encode($response), true);
    }

    /**
     * Check if the response indicates success.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->statusCode === 0 && empty($this->errors);
    }

    /**
     * Get the status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the remark message.
     *
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * Get the data object.
     *
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * Get the errors array.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the raw response as array.
     *
     * @return array|null
     */
    public function getRawResponse(): ?array
    {
        return $this->rawResponse;
    }

    /**
     * Check if the response has any errors.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get specific field from data.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getDataField(string $key, $default = null)
    {
        return $this->data->$key ?? $default;
    }

    /**
     * Check if data has a specific field.
     *
     * @param string $key
     * @return bool
     */
    public function hasDataField(string $key): bool
    {
        return isset($this->data->$key);
    }
}