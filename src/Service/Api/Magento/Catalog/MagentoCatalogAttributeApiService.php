<?php

namespace App\Service\Api\Magento\Catalog;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\AttributeInput;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\InputObject;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Twig\Global\Core\StoreConfig;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCatalogAttributeApiService
{
    public function __construct(
        protected readonly MageGraphQlClient $mageGraphQlClient,
        protected readonly StoreConfig       $storeConfig,
        protected readonly RedisAdapter      $redisAdapter,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function collectAttributeMetaData(string $attributeCode, string $type = 'catalog_product'): array
    {
        $cacheKey = "catalog_attribute_metadata_{$attributeCode}_{$type}";
        return $this->redisAdapter->get($cacheKey, function (ItemInterface $item) use ($attributeCode, $type) {
            $item->expiresAfter(24 * 60 * 60);

            $response = (new Request(
                (new Query('customAttributeMetadata')
                )->addParameter(
                    (new AttributeInput())
                        ->addAttributes(
                            [
                                (new InputObject()
                                )->addInputFields(
                                    [
                                        new InputField('attribute_code', $attributeCode),
                                        new InputField('entity_type', $type),
                                    ]
                                ),
                            ]
                        )
                )->addField(
                    (new Field('items')
                    )->addChildFields(
                        [
                            new Field('attribute_code'),
                            new Field('attribute_type'),
                            new Field('entity_type'),
                            new Field('input_type'),
                            (new Field('attribute_options'))->addChildFields(
                                [
                                    new Field('value'),
                                    new Field('label'),
                                ]
                            ),
                        ]
                    )
                ),
                $this->mageGraphQlClient)
            )->send();

            return (array)(current($response['data']['customAttributeMetadata']['items']) ?? []);
        });
    }
}