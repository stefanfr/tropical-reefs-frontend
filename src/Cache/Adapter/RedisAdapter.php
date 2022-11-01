<?php

namespace App\Cache\Adapter;

use Predis\ClientInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter as RedisAdapterCore;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;
use Symfony\Component\DependencyInjection\Container;

class RedisAdapter extends RedisAdapterCore
{
    public function __construct(Container $container, string $namespace = '', int $defaultLifetime = 0, MarshallerInterface $marshaller = null)
    {
        parent::__construct(
            RedisAdapterCore::createConnection($container->getParameter('redis_server_url')),
            $namespace,
            $defaultLifetime,
            $marshaller
        );
    }
}