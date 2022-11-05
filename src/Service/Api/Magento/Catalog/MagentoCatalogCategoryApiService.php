<?php

namespace App\Service\Api\Magento\Catalog;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\Api\Magento\Http\MageRestClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Filter;
use App\Service\GraphQL\Filters;
use App\Service\GraphQL\Fragment;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCatalogCategoryApiService
{
    public function __construct(
        protected MageRestClient    $mageRestClient,
        protected MageGraphQlClient $mageGraphQlClient,
        protected RedisAdapter      $redisAdapter,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function collectCategoryTree(?string $uid = null, bool $debug = false): array
    {
        $cacheKey = 'catalog_category_tree';
        if (null !== $uid) {
            $cacheKey .= '_' . $uid;
            $this->redisAdapter->clear($cacheKey);
        }

        return $this->redisAdapter->get($cacheKey, function (ItemInterface $item) use ($uid, $debug) {
            $item->expiresAfter(24 * 60 * 60);

            $parentId = '2';

            if (null !== $uid) {
                $parentId = base64_decode($uid);
            }

            try {
                $response = (new Request(
                    (new Query('categoryList')
                    )->addParameter(
                        (new Filters)
                            ->addFilter(
                                (new Filter('parent_id'))
                                    ->addOperator(
                                        'in',
                                        [$parentId]
                                    ),
                            ),
                    )->addFields(
                        [
                            new Field('children_count'),
                            new Field('id'),
                            new Field('name'),
                            new Field('path'),
                            new Field('url_path'),
                            (new Field('children')
                            )->addChildFields(
                                [
                                    new Field('id'),
                                    new Field('name'),
                                    new Field('path'),
                                    new Field('url_path'),
                                    (new Field('children')
                                    )->addChildFields(
                                        [
                                            new Field('id'),
                                            new Field('name'),
                                            new Field('path'),
                                            new Field('url_path'),
                                            (new Field('children')
                                            )->addChildFields(
                                                [
                                                    new Field('id'),
                                                    new Field('name'),
                                                    new Field('path'),
                                                    new Field('url_path'),
                                                    (new Field('children')
                                                    )->addChildFields(
                                                        [
                                                            new Field('id'),
                                                            new Field('name'),
                                                            new Field('path'),
                                                            new Field('url_path'),
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
                    , $this->mageGraphQlClient
                ))->send();

                if ($debug) {
                    die(json_encode($response['data']['categoryList'] ?? $response, JSON_THROW_ON_ERROR));
                }

                return $response['data']['categoryList'];
            } catch (Exception $e) {
                return [];
            }
        });
    }

    public function collectCategory(string $uid, bool $debug = false): array
    {
        return $this->redisAdapter->get(
            'catalog_category_products_' . $uid,
            function (ItemInterface $item) use ($uid, $debug) {
                $item->expiresAfter(1 * 60 * 60);

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
                            new Field('total_count'),
                            (new Field('page_info')
                            )->addChildFields(
                                [
                                    new Field('current_page'),
                                    new Field('page_size'),
                                    new Field('total_pages'),
                                ]
                            ),
                            (new Field('sort_fields')
                            )->addChildFields(
                                [
                                    new Field('default'),
                                    (new Field('options')
                                    )->addChildFields(
                                        [
                                            new Field('label'),
                                            new Field('value'),
                                        ]
                                    ),
                                ]
                            ),
                            (new Field('items')
                            )->addChildFields(
                                [
                                    new Field('uid'),
                                    new Field('type_id'),
                                    new Field('url_key'),
                                    new Field('name'),
                                    new Field('sku'),
                                    (new Field('custom_attributes')
                                    )->addChildFields(
                                        [
                                            new Field('code'),
                                            new Field('value'),
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
                                                            (new Field('name')),
                                                            (new Field('sku')),
                                                            (new Field('size')),
                                                            (new Field('custom_attributes')
                                                            )->addChildFields(
                                                                [
                                                                    new Field('code'),
                                                                    new Field('value'),
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
                    die(json_encode($response['data']['products'] ?? $response, JSON_THROW_ON_ERROR));
                }

                return $response['data']['products'] ?? throw new NotFoundHttpException('Category not found');
            }
        );
    }

    public function collectHomeCategories($debug = false)
    {
        return $this->redisAdapter->get('catalog_category_homepage',
            function (ItemInterface $item) use ($debug) {
                $item->expiresAfter(24 * 60 * 60);

                try {
                    $response = (new Request(
                        (new Query('categoryList')
                        )->addParameter(
                            (new Filters)
                                ->addFilter(
                                    (new Filter('show_on_homepage'))
                                        ->addOperator(
                                            'eq',
                                            1
                                        ),
                                )->addFilter(
                                    (new Filter('is_brand'))
                                        ->addOperator(
                                            'null',
                                            ''
                                        ),
                                ),
                        )->addFields(
                            [
                                new Field('name'),
                                new Field('image'),
                                new Field('url_path'),
                                new Field('homepage_position'),
                            ]
                        )
                        , $this->mageGraphQlClient
                    ))->send();

                    if ($debug) {
                        die(json_encode($response['data']['categoryList'] ?? $response, JSON_THROW_ON_ERROR));
                    }

                    return $response['data']['categoryList'];
                } catch (Exception $e) {
                    return [];
                }
            }
        );
    }

    public function collectHomeBrandCategories($debug = false)
    {
        return $this->redisAdapter->get('catalog_category_brand_homepage',
            function (ItemInterface $item) use ($debug) {
                $item->expiresAfter(24 * 60 * 60);

                try {
                    $response = (new Request(
                        (new Query('categoryList')
                        )->addParameter(
                            (new Filters)
                                ->addFilter(
                                    (new Filter('show_on_homepage'))
                                        ->addOperator(
                                            'eq',
                                            1
                                        ),
                                )->addFilter(
                                    (new Filter('is_brand'))
                                        ->addOperator(
                                            'eq',
                                            1
                                        ),
                                ),
                        )->addFields(
                            [
                                new Field('image'),
                                new Field('url_path'),
                                new Field('homepage_position'),
                            ]
                        )
                        , $this->mageGraphQlClient
                    ))->send();

                    if ($debug) {
                        die(json_encode($response['data']['categoryList'] ?? $response, JSON_THROW_ON_ERROR));
                    }

                    return $response['data']['categoryList'];
                } catch (Exception $e) {
                    return [];
                }
            }
        );
    }
}