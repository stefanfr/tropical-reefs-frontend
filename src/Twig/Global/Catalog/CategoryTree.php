<?php

namespace App\Twig\Global\Catalog;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Catalog\MagentoCatalogCategoryApiService;
use Psr\Cache\InvalidArgumentException;

class CategoryTree
{
    public function __construct(
        protected MagentoCatalogCategoryApiService $catalogCategoryApiService,
        protected RedisAdapter                     $redisAdapter
    )
    {
    }

    public function collect(): array
    {
        try {
            return $this->catalogCategoryApiService->collectCategoryTree();
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }
}