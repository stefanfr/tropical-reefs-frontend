<?php

namespace App\Service\Api\Magento\Catalog;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Filter;
use App\Service\GraphQL\Filters;
use App\Service\GraphQL\Fragment;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\InputObject;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Twig\Global\Core\StoreConfig;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCatalogCategoryApiService
{
    public function __construct(
        protected MageGraphQlClient $mageGraphQlClient,
        protected StoreConfig       $storeConfig,
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

            $parentId = $uid ?? $this->storeConfig->getData('root_category_uid');

            try {
                $response = (new Request(
                    (new Query('categoryList')
                    )->addParameter(
                        (new Filters)
                            ->addFilter(
                                (new Filter('include_in_menu'))
                                    ->addOperator(
                                        'eq',
                                        1
                                    ),
                            )
                            ->addFilter(
                                (new Filter('parent_category_uid'))
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
                            new Field('product_count'),
                            (new Field('children')
                            )->addChildFields(
                                [
                                    new Field('id'),
                                    new Field('name'),
                                    new Field('path'),
                                    new Field('url_path'),
                                    new Field('product_count'),
                                    (new Field('children')
                                    )->addChildFields(
                                        [
                                            new Field('id'),
                                            new Field('name'),
                                            new Field('path'),
                                            new Field('url_path'),
                                            new Field('product_count'),
                                            (new Field('children')
                                            )->addChildFields(
                                                [
                                                    new Field('id'),
                                                    new Field('name'),
                                                    new Field('path'),
                                                    new Field('url_path'),
                                                    new Field('product_count'),
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
        if ($debug) {
            $this->redisAdapter->delete('catalog_category_products_' . $uid);
        }

        return $this->redisAdapter->get(
            'catalog_category_products_' . $uid,
            function (ItemInterface $item) use ($uid, $debug) {
                $item->expiresAfter(24 * 60 * 60);

                $response = (new Request(
                    (new Query('category')
                    )->addParameter(
                        new Parameter('id', base64_decode($uid)),
                    )->addFields(
                        [
                            new Field('name'),
                            new Field('description'),
                            new Field('product_count'),
                            new Field('url_path'),
                            (new Field('breadcrumbs')
                            )->addChildFields(
                                [
                                    new Field('category_level'),
                                    new Field('category_name'),
                                    new Field('category_uid'),
                                    new Field('category_url_key'),
                                    new Field('category_url_path'),
                                ]
                            ),
                            (new Field('children')
                            )->addChildFields(
                                [
                                    new Field('uid'),
                                    new Field('name'),
                                    new Field('path'),
                                    new Field('url_path'),
                                    new Field('product_count'),
                                ]
                            ),
                        ]
                    )
                    ,
                    $this->mageGraphQlClient
                )
                )->send();

                if ($debug) {
                    die(json_encode($response['data']['category'] ?? $response, JSON_THROW_ON_ERROR));
                }

                return $response['data']['category'] ?? throw new NotFoundHttpException('Category not found');
            }
        );
    }

    public function collectCategoryProducts(string $categoryUid, array $options = [], array $filters = [], bool $debug = false): array
    {
        $customFilter = new Filters('filter');

        if ( ! empty($filters)) {
            foreach ($filters as $filter) {
                $customFilter->addFilter(
                    (new Filter($filter['attribute']))
                        ->addOperator(
                            $filter['operator'],
                            $filter['value']
                        ),
                );
            }
        }

        $parameters = [
            $customFilter->addFilter(
                (new Filter('category_uid'))
                    ->addOperator(
                        'eq',
                        $categoryUid
                    ),
            ),
        ];

        if ( ! empty($options)) {
            if (isset($options['search'])) {
                $parameters[] = new InputField('search', $options['search']);
            }
            if (isset($options['pageSize'])) {
                $parameters[] = new InputField('pageSize', $options['pageSize']);
            }
            if (isset($options['currentPage'])) {
                $parameters[] = new InputField('currentPage', $options['currentPage']);
            }
            if (isset($options['sort'])) {
                $parameters[] = new InputObject('sort', [
                    new InputField($options['sort']['value'], $options['sort']['direction']),
                ]);
            }
        }

        return $this->redisAdapter->get(
            'catalog_category_products_' . sha1(serialize($parameters)),
            function (ItemInterface $item) use ($parameters) {
                $item->expiresAfter(4 * 60 * 60);

                $response = (new Request(
                    (new Query('products', $parameters)
                    )->addFields(
                        [
                            new Field('total_count'),
                            (new Field('items')
                            )->addChildFields(
                                [
                                    new Field('uid'),
                                    new Field('type_id'),
                                    new Field('url_key'),
                                    new Field('name'),
                                    new Field('sku'),
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
                                                        ]
                                                    ),
                                                ]
                                            ),
                                        ]
                                    ),
                                ]
                            ),
                            (new Field('aggregations')
                            )->addChildFields(
                                [
                                    new Field('count'),
                                    new Field('label'),
                                    new Field('position'),
                                    new Field('attribute_code'),
                                    (new Field('options')
                                    )->addChildFields(
                                        [
                                            new Field('count'),
                                            new Field('label'),
                                            new Field('value'),
                                        ]
                                    ),
                                ]
                            ),
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
                            (new Field('suggestions')
                            )->addChildFields(
                                [
                                    new Field('search'),
                                ]
                            ),
                        ]
                    ),
                    $this->mageGraphQlClient
                ))->send();

                return $response['data']['products'] ?? $response['errors'];
            }
        );
    }

    public function collectHomeCategories($debug = false)
    {
        if ($debug) {
            $this->redisAdapter->delete('catalog_category_homepage');
        }

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
                                            'eq',
                                            0
                                        ),
                                ),
                        )->addFields(
                            [
                                new Field('name'),
                                new Field('image'),
                                new Field('url_path'),
                                new Field('is_brand'),
                                new Field('homepage_position'),
                                new Field('show_on_homepage'),
                            ]
                        ),
                        $this->mageGraphQlClient
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
}