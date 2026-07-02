<?php

namespace App\EIS\Services\Utilities;

use App\EIS\Services\Http\HttpClientService;

class UtilityService
{
    public function __construct(
        protected HttpClientService $http
    ) {}

    /**
     * Health check / ping EIS
     */
    public function ping(): array
    {
        return $this->http->get('utilities/ping');
    }

    /**
     * Validate VAT certificate
     */
    public function validateVatCertificate(string $vatNumber): array
    {
        return $this->http->post('utilities/validate-vat-certificate', [
            'vatNumber' => $vatNumber
        ]);
    }

    /**
     * Validate authorization code
     */
    public function validateAuthorizationCode(string $code): array
    {
        return $this->http->post('utilities/validate-authorization-code', [
            'authorizationCode' => $code
        ]);
    }

    /**
     * Check if TIN requires authorization
     */
    public function checkTinAuthorization(string $tin): array
    {
        return $this->http->post('utilities/check-tin-authorization-requirement', [
            'tin' => $tin
        ]);
    }

    /**
     * Terminal blocking status
     */
    public function terminalBlockingMessage(): array
    {
        return $this->http->get('utilities/terminal-blocking-message');
    }

    /**
     * Check if terminal is unblocked
     */
    public function checkUnblockStatus(): array
    {
        return $this->http->get('utilities/check-terminal-unblock-status');
    }

    /**
     * Get products assigned to terminal
     */
    public function getTerminalProducts(): array
    {
        return $this->http->get('utilities/terminal-products');
    }

    /**
     * Check product status
     */
    public function productStatus(string $productCode): array
    {
        return $this->http->post('utilities/product-status', [
            'productCode' => $productCode
        ]);
    }

    /**
     * Upload initial inventory
     */
    public function uploadInitialInventory(array $items): array
    {
        return $this->http->post('utilities/upload-initial-inventory', [
            'items' => $items
        ]);
    }
}