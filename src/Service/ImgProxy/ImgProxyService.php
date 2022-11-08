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

        $path = "/{$filter}/{$encodedUrl}{$extends}";

//        $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $saltBin . $path, $keyBin, true)), '+/', '-_'), '=');

        return $this->getBaseUrl() . sprintf('/%s', $path);
    }
}