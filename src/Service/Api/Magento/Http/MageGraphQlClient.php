<?php

namespace App\Service\Api\Magento\Http;

class MageGraphQlClient extends MageHttpClient
{
    protected ?string $baseUrl = null;

    /**
     * @param null|string $storeCode
     * @return string
     */
    public function getApiUrl(?string $storeCode = 'default'): string
    {
        $url = $this->getApiBaseUrl();
        $url .= null !== $storeCode ? "/{$storeCode}" : '';

        return $url;
    }

    /**
     * Get the base URL of the REST client.
     *
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return rtrim($this->baseUrl ?? $this->getBaseUrl(), '/') . '/graphql';
    }

    public function setBaseUrl(?string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }
}