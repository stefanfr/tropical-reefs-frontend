<?php

namespace App\Service\Imaginary\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImaginaryHttpClient
{
    protected ClientInterface|Client $client;
    protected array $config;

    public function __construct(
        protected LoggerInterface    $logger,
        protected ContainerInterface $container,
        ?array                       $options = []
    )
    {
        $this->config = $container->getParameter('imaginary_client');
        $this->client = new Client(
            array_replace($this->getDefaultClientOptions(), $options)
        );
    }

    /**
     * @param MessageInterface $request
     * @param array $options
     * @return ?ResponseInterface
     */
    public function send(MessageInterface $request, array $options = []): ?ResponseInterface
    {
        try {
            return $this->client->send($request, $options);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());
            return null;
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

    protected function getBaseUrl(): string
    {
        return $this->config['base_url'];
    }

    public function getAppUrl(): string
    {
        return $this->config['app_url'];
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

    public function isEnabled(): bool
    {
        return $this->config['enabled'];
    }

    public function getCdnUrl(string $uri, string $method, int|string $width, int $height): string
    {
        return implode('/', [
                $this->getBaseUrl(),
                $method,
                $width,
                $height,
            ]) . preg_replace('/cache\/.*\//U', '', $uri);
    }
}