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
            return array_filter(
                $this->catalogCategoryApiService->collectCategoryTree(),
                static function ($category) {
                    return $category['include_in_menu'];
                }
            );
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }
}