<?php

namespace App\Service\Api\Magento\Http;

use App\Service\Api\Http\HttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MageHttpClient extends HttpClient
{
    protected ClientInterface|Client $client;
    protected array $config;

    public function __construct(
        protected ContainerInterface $container,
        ?array                       $options = []
    )
    {
        $this->config = $container->getParameter('mage_client');
        $this->client = new Client(
            array_replace($this->getDefaultClientOptions(), $options ?? [])
        );
    }

    protected function getBaseUrl(): string
    {
        return $this->config['api']['base_url'];
    }

    /**
     * @return string[]
     */
    protected function getDefaultClientOptions(): array
    {
        return array_merge(
            [
                'base_uri' => $this->getBaseUrl(),
            ],
            $this->config['api']['default_headers'] ?? [],
        );
    }
}