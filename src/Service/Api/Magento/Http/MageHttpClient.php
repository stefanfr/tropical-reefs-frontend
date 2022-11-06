<?php

namespace App\Service\Api\Magento\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MageHttpClient
{
    protected ClientInterface|Client $client;
    protected array $config;

    public function __construct(
        protected ContainerInterface $container,
        ?array                        $options = []
    )
    {
        $this->config = $container->getParameter('mage_client');
        $this->client = new Client(
            array_replace($this->getDefaultClientOptions(), $options ?? [])
        );
    }

    /**
     * @param MessageInterface $request
     * @param array $options
     * @return ResponseInterface
     */
    public function send(MessageInterface $request, array $options = []): ResponseInterface
    {
        try {
            return $this->client->send($request, $options);
        } catch (GuzzleException $e) {
        }
    }

    /**
     * @param string $uri
     * @return MessageInterface
     */
    public function get(string $uri): MessageInterface
    {
        return new Request('GET', $uri);
    }

    /**
     * @param string $uri
     * @param string|null $body
     * @return MessageInterface
     */
    public function post(string $uri, ?string $body = null): MessageInterface
    {
        return (new Request('POST', $uri))->withBody(Utils::streamFor($body));
    }

    /**
     * @param string $uri
     * @param null|array $body
     * @return MessageInterface
     */
    public function put(string $uri, ?array $body = null): MessageInterface
    {
        return (new Request('PUT', $uri))->withBody(Utils::streamFor($body));
    }

    /**
     * @param string $uri
     * @param null|array $body
     * @return MessageInterface
     */
    public function delete(string $uri, ?array $body = null): MessageInterface
    {
        return (new Request('DELETE', $uri))->withBody(Utils::streamFor($body));
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