<?php

namespace App\Twig\Global\Core;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Core\MagentoCoreStoreConfigService;

class StoreConfig
{
    protected mixed $storeConfigData;

    public function __construct(
        protected MagentoCoreStoreConfigService $magentoCoreStoreConfigService,
        protected RedisAdapter                  $redisAdapter
    )
    {
        $this->storeConfigData = $this->magentoCoreStoreConfigService->collectStoreConfig();
    }

    public function getData(string $key): mixed
    {
        return $this->storeConfigData[$key];
    }
}