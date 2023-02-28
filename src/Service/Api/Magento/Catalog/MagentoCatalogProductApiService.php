<?php

namespace App\Service\Api\Magento\Catalog;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Core\MagentoCoreStoreConfigService;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Filter;
use App\Service\GraphQL\Filters;
use App\Service\GraphQL\Fragment;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Service\GraphQL\Selection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCatalogProductApiService
{
    public function __construct(
        protected readonly RedisAdapter                  $redisAdapter,
        protected readonly MageGraphQlClient             $mageGraphQlClient,
        protected readonly MagentoCoreStoreConfigService $magentoCoreStoreConfigService,
    )
    {
    }

    public function collectProduct(string $uid, $selectedOptions = [], bool $debug = false): array
    {
        $cacheKey = 'catalog_product_' . $uid;

        if ( ! empty($selectedOptions)) {
            $cacheKey .= '_' . implode('_', $selectedOptions);
        }

        if ($debug) {
            $this->redisAdapter->clear($cacheKey);
        }

        return $this->redisAdapter->get($cacheKey,
            function (ItemInterface $item) use ($uid, $debug, $selectedOptions) {
                $item->expiresAfter(24 * 60 * 60);

                $response = (new Request(
                    (new Query('product')
                    )->addParameter(
                        (new Filters('filter')
                        )->addFilter(
                            (new Filter('uid')
                            )->addOperator(
                                'eq',
                                $uid
                            )
                        )
                    )->addFields(
                        [
                            (new Field('product')
                            )->addChildFields(
                                [
                                    (new Fragment('ConfigurableProduct')
                                    )->addFields(
                                        [
                                            (new Field('variants')
                                            )->addChildFields(
                                                [
                                                    (new Field('product')
                                                    )->addChildFields(
                                                        [
                                                            new Field('sku'),
                                                            new Field('size'),
                                                            new Field('stock_status'),
                                                            new Field('only_x_left_in_stock'),
                                                        ]
                                                    ),
                                                ]
                                            ),
                                        ]
                                    ),
                                    new Field('uid'),
                                    new Field('type_id'),
                                    new Field('url_key'),
                                    new Field('name'),
                                    new Field('sku'),
                                    new Field('stock_status'),
                                    new Field('only_x_left_in_stock'),
                                    (new Field('description')
                                    )->addChildField(
                                        new Field('html')
                                    ),
                                    (new Field('short_description')
                                    )->addChildField(
                                        new Field('html')
                                    ),
                                    (new Field('small_image')
                                    )->addChildFields(
                                        [
                                            new Field('url'),
                                            new Field('label'),
                                        ]
                                    ),
                                    (new Field('custom_attributes')
                                    )->addChildFields(
                                        [
                                            new Field('code'),
                                            new Field('label'),
                                            new Field('value'),
                                        ]
                                    ),
                                    (new Field('media_gallery')
                                    )->addChildFields(
                                        [
                                            new Field('url'),
                                            new Field('label'),
                                            new Field('position'),
                                            (new Fragment('ProductVideo')
                                            )->addField(
                                                (new Field('video_content')
                                                )->addChildFields(
                                                    [
                                                        new Field('media_type'),
                                                        new Field('video_provider'),
                                                        new Field('video_url'),
                                                        new Field('video_title'),
                                                        new Field('video_description'),
                                                        new Field('video_metadata'),
                                                    ]
                                                ),
                                            ),
                                        ]
                                    ),
                                    (new Field('price_range')
                                    )->addChildField(
                                        (new Field('minimum_price')
                                        )->addChildFields(
                                            [
                                                (new Field('final_price'))
                                                    ->addChildFields(
                                                        [
                                                            new Field('value'),
                                                            new Field('currency'),
                                                        ]
                                                    ),
                                                (new Field('regular_price'))
                                                    ->addChildFields(
                                                        [
                                                            new Field('value'),
                                                            new Field('currency'),
                                                        ]
                                                    ),
                                                (new Field('discount'))
                                                    ->addChildFields(
                                                        [
                                                            new Field('amount_off'),
                                                            new Field('percent_off'),
                                                        ]
                                                    ),
                                            ]
                                        )
                                    ),
                                    (new Field('related_products')
                                    )->addChildFields(
                                        [
                                            new Field('uid'),
                                            new Field('type_id'),
                                            new Field('url_key'),
                                            new Field('name'),
                                            new Field('review_count'),
                                            new Field('rating_summary'),
                                            new Field('sku'),
                                            new Field('size'),
                                            (new Field('price_range')
                                            )->addChildField(
                                                (new Field('minimum_price')
                                                )->addChildFields(
                                                    [
                                                        (new Field('final_price'))
                                                            ->addChildFields(
                                                                [
                                                                    new Field('value'),
                                                                    new Field('currency'),
                                                                ]
                                                            ),
                                                        (new Field('regular_price'))
                                                            ->addChildFields(
                                                                [
                                                                    new Field('value'),
                                                                    new Field('currency'),
                                                                ]
                                                            ),
                                                        (new Field('discount'))
                                                            ->addChildFields(
                                                                [
                                                                    new Field('amount_off'),
                                                                    new Field('percent_off'),
                                                                ]
                                                            ),
                                                    ]
                                                )
                                            ),
                                            (new Field('small_image')
                                            )->addChildFields(
                                                [
                                                    new Field('url'),
                                                    new Field('label'),
                                                ]
                                            ),
                                            (new Fragment('ConfigurableProduct')
                                            )->addFields(
                                                [
                                                    (new Field('variants')
                                                    )->addChildFields(
                                                        [
                                                            (new Field('product')
                                                            )->addChildFields(
                                                                [
                                                                    new Field('sku'),
                                                                    new Field('size'),
                                                                ]
                                                            ),
                                                        ]
                                                    ),
                                                ]
                                            ),
                                        ]
                                    ),
                                    (new Fragment('ConfigurableProduct')
                                    )->addFields(
                                        [
                                            (new Selection('configurable_product_options_selection')
                                            )->addParameter(
                                                new Parameter('configurableOptionValueUids', $selectedOptions)
                                            )->addChildFields(
                                                [
                                                    (new Field('variant')
                                                    )->addChildFields(
                                                        [
                                                            new Field('sku'),
                                                            new Field('stock_status'),
                                                            new Field('only_x_left_in_stock'),
                                                            (new Field('price_range')
                                                            )->addChildField(
                                                                (new Field('minimum_price')
                                                                )->addChildFields(
                                                                    [
                                                                        (new Field('final_price'))
                                                                            ->addChildFields(
                                                                                [
                                                                                    new Field('value'),
                                                                                    new Field('currency'),
                                                                                ]
                                                                            ),
                                                                        (new Field('regular_price'))
                                                                            ->addChildFields(
                                                                                [
                                                                                    new Field('value'),
                                                                                    new Field('currency'),
                                                                                ]
                                                                            ),
                                                                        (new Field('discount'))
                                                                            ->addChildFields(
                                                                                [
                                                                                    new Field('amount_off'),
                                                                                    new Field('percent_off'),
                                                                                ]
                                                                            ),
                                                                    ]
                                                                )
                                                            ),
                                                        ]
                                                    ),
                                                    (new Field('options_available_for_selection')
                                                    )->addChildFields(
                                                        [
                                                            new Field('attribute_code'),
                                                            new Field('option_value_uids'),
                                                        ]
                                                    ),
                                                ]
                                            ),
                                            (new Field('configurable_options')
                                            )->addChildFields(
                                                [
                                                    new Field('attribute_code'),
                                                    new Field('attribute_uid'),
                                                    new Field('label'),
                                                    (new Field('values')
                                                    )->addChildFields(
                                                        [
                                                            new Field('uid'),
                                                            new Field('store_label'),
                                                            new Field('use_default_value'),
                                                            (new Field('swatch_data')
                                                            )->addChildFields(
                                                                [
                                                                    new Field('value'),
                                                                    (new Fragment('ImageSwatchData')
                                                                    )->addField(
                                                                        new Field('thumbnail'),
                                                                    ),
                                                                ]
                                                            ),
                                                        ]
                                                    ),
                                                ]
                                            ),
                                        ]
                                    ),
                                ]
                            ),
                        ]
                    )
                    ,
                    $this->mageGraphQlClient
                )
                )->send();

                if ($debug) {
                    dd(
                        $response['data']['product']['product'] ?? $response
                    );
                }

                return $response['data']['product']['product'] ?? throw new NotFoundHttpException('Product not found');
            }
        );
    }

    public function collectHomeFeaturedProducts($debug = false)
    {
        $uid = base64_encode($this->magentoCoreStoreConfigService->getStoreConfigData('featured_category_id'));
        return $this->redisAdapter->get('catalog_home_featured_products_' . $uid,
            function (ItemInterface $item) use ($uid, $debug) {
                $item->expiresAfter(24 * 60 * 60);

                $response = (new Request(
                    (new Query('products')
                    )->addParameter(
                        (new Filters('filter')
                        )->addFilter(
                            (new Filter('category_uid')
                            )->addOperator(
                                'eq',
                                $uid
                            )
                        )
                    )->addFields(
                        [
                            (new Field('items')
                            )->addChildFields(
                                [
                                    new Field('uid'),
                                    new Field('url_key'),
                                    new Field('name'),
                                    new Field('type_id'),
                                    new Field('sku'),
                                    (new Field('small_image')
                                    )->addChildFields(
                                        [
                                            new Field('url'),
                                        ]
                                    ),
                                    (new Field('price_range')
                                    )->addChildField(
                                        (new Field('minimum_price')
                                        )->addChildFields(
                                            [
                                                (new Field('final_price'))
                                                    ->addChildFields(
                                                        [
                                                            new Field('value'),
                                                            new Field('currency'),
                                                        ]
                                                    ),
                                                (new Field('regular_price'))
                                                    ->addChildFields(
                                                        [
                                                            new Field('value'),
                                                            new Field('currency'),
                                                        ]
                                                    ),
                                                (new Field('discount'))
                                                    ->addChildFields(
                                                        [
                                                            new Field('amount_off'),
                                                            new Field('percent_off'),
                                                        ]
                                                    ),
                                            ]
                                        )
                                    ),
                                ]
                            ),
                        ]
                    )
                    ,
                    $this->mageGraphQlClient
                )
                )->send();

                if ($debug) {
                    echo json_encode($response['data']['products']['items'] ?? $response, JSON_THROW_ON_ERROR);
                    die;
                }

                return $response['data']['products']['items'] ?? throw new NotFoundHttpException('Product not found');
            }
        );
    }
}