<?php

namespace App\Twig\Global\Core;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Core\MagentoCodeStoreConfigService;
use Psr\Cache\InvalidArgumentException;

class StoreConfig
{
    protected mixed $storeConfigData;

    public function __construct(
        protected MagentoCodeStoreConfigService $magentoCodeStoreConfigService,
        protected RedisAdapter                  $redisAdapter
    )
    {
        $this->storeConfigData = $this->magentoCodeStoreConfigService->collectStoreConfig();
    }

    public function getData(string $key): mixed
    {
        return $this->storeConfigData[$key];
    }
}