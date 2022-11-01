<?php

namespace App\Twig\Global\Catalog;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Catalog\MagentoCatalogCategoryApiService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

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
            return $this->redisAdapter->get('catalog_category_tree', function (ItemInterface $item) {
                $item->expiresAfter(24 * 60 * 60);

                return $this->catalogCategoryApiService->collectCategoryTree();
            });
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }
}