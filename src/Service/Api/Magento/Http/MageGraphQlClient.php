<?php

namespace App\Service\Api\Magento\Http;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MageGraphQlClient extends MageHttpClient
{
    protected ?string $baseUrl = null;

    public function __construct(
        ContainerInterface     $container,
        protected RequestStack $requestStack,
        ?array                 $options = []
    )
    {
        parent::__construct($container, $options);
    }

    /**
     * @inheritdoc
     */
    public function send(MessageInterface $request, array $options = []): ResponseInterface
    {
        try {
            $session = $this->requestStack->getSession();
            if ($session->has('customerToken')) {
                $options['headers']['Authorization'] = 'Bearer ' . $session->get('customerToken');
            }

            return $this->client->send($request, $options);
        } catch (GuzzleException $e) {
            return $e->getResponse();
        }
    }

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