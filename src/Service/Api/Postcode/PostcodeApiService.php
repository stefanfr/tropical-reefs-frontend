<?php

namespace App\Service\Api\Postcode;

use App\Service\Api\Http\HttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PostcodeApiService extends HttpClient
{
    protected ClientInterface|Client $client;
    protected array $config;

    public function __construct(
        protected ContainerInterface $container,
        ?array                       $options = []
    )
    {
        $this->config = $container->getParameter('postcode_api');
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
        );
    }
}