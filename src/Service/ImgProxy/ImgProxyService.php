<?php

namespace App\Service\ImgProxy;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImgProxyService
{
    protected array $config;

    public function __construct(
        protected LoggerInterface    $logger,
        protected ContainerInterface $container,
    )
    {
        $this->config = $container->getParameter('imgproxy');
    }

    protected function getBaseUrl(): string
    {
        return $this->config['base_url'];
    }

    protected function getKey(): string
    {
        return $this->config['key'];
    }

    protected function getSalt(): string
    {
        return $this->config['salt'];
    }

    public function getUrl(string $url, string $filter, string $extends = '.webp')
    {
        $keyBin = pack('H*', $this->getKey());
        if (empty($keyBin)) {
            die('Key expected to be hex-encoded string');
        }
        $saltBin = pack('H*', $this->getSalt());
        if (empty($saltBin)) {
            die('Salt expected to be hex-encoded string');
        }

        $encodedUrl = rtrim(strtr(base64_encode($url), '+/', '-_'), '=');

        $path = "{$filter}/{$encodedUrl}{$extends}";

//        $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $saltBin . $path, $keyBin, true)), '+/', '-_'), '=');

        return $this->getBaseUrl() . sprintf('/%s', $path);
    }

    public function getCdnUrl(?string $uri, string $method, int|string $width, ?int $height = null): string
    {
        if (null === $uri) {
            $uri = match ($width) {
                'square' => sprintf('https://via.placeholder.com/%s', $height),
                default => sprintf('https://via.placeholder.com/%sx%s', $width, $height),
            };
        }

        $uri = str_replace('aquastore.test', 'dev.tropicalreefs.nl', $uri);
        if ( ! filter_var($uri, FILTER_VALIDATE_URL)) {
            $uri = 'https://dev.tropicalreefs.nl/'.$uri;
        }

        $filters = 'pr:sharp/';

        $filters .= match ($method) {
            'crop' => 'crop:',
            'fit' => 'rs:fit:',
            'fill' => 'rs:fill:',
            'fill-down' => 'rs:fill-down:',
            'force' => 'rs:force:',
            'auto' => 'rs:auto:',
            default => 'plain',
        };

        $filters .= match ($width) {
            'square' => sprintf('%s:%s', $height, $height),
            default => sprintf('%s:%s', $width, $height),
        };

        $filters .= '/ex:1';
        $filters .= '/g:ce';
        $filters .= '/ar:1';

        return $this->getUrl(
            preg_replace('/cache\/.*\//U', '', $uri),
            $filters
        );
    }
}