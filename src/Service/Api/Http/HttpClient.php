<?php

namespace App\Service\Api\Http;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;

abstract class HttpClient
{
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
            return $e->getResponse();
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
}