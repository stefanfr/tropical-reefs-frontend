<?php

namespace App\Service\Api\Magento\Http;

class MageGraphQlClient extends MageHttpClient
{
    /**
     * @param string $uri
     * @param null|string $storeCode
     * @return string
     */
    public function getApiUrl(string $uri, ?string $storeCode = 'default'): string
    {
        $url = $this->getApiBaseUrl();
        $url .= null !== $storeCode ? "/{$storeCode}" : '';
        $url .= "/V1";
        $url .= "/{$uri}";

        return $url;
    }

    /**
     * Get the base URL of the REST client.
     *
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return rtrim($this->getBaseUrl(), '/') . '/graphql';
    }
}