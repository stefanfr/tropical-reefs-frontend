<?php

namespace App\Service\Api\Magento\Core;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCoreStoreConfigService
{
    protected array $storeConfig = [];

    public function __construct(
        protected MageGraphQlClient $mageGraphQlClient,
        protected RedisAdapter      $redisAdapter,
    )
    {
        $this->collectStoreConfig();
    }

    public function getStoreConfigData(string $key): mixed
    {
        return $this->storeConfig[$key];
    }

    public function collectStoreConfig(): array
    {
        if ( ! empty($this->storeConfig)) {
            return $this->storeConfig;
        }

        return $this->storeConfig = $this->redisAdapter->get('magento_core_store_config', function (ItemInterface $item) {
            $response = (new Request(
                (new Query('storeConfig')
                )->addFields(
                    [
                        new Field('locale'),
                        new Field('timezone'),
                        new Field('base_url'),
                        new Field('store_name'),
                        new Field('store_code'),
                        new Field('weight_unit'),
                        new Field('default_title'),
                        new Field('secure_base_url'),
                        new Field('default_keywords'),
                        new Field('root_category_uid'),
                        new Field('is_default_store'),
                        new Field('featured_category_id'),
                        new Field('store_group_code'),
                        new Field('base_currency_code'),
                        new Field('grid_per_page'),
                        new Field('grid_per_page_values'),
                    ]
                ),
                $this->mageGraphQlClient
            ))->send();

            return $response['data']['storeConfig'] ?? [];
        });
    }
}