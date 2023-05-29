<?php

namespace App\Service\Api\Magento\Catalog;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Core\MagentoCoreStoreConfigService;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Filter;
use App\Service\GraphQL\Filters;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Service\GraphQL\Types\ProductType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCatalogProductApiService
{
    public function __construct(
        protected readonly RedisAdapter $redisAdapter,
        protected readonly MageGraphQlClient $mageGraphQlClient,
        protected readonly MagentoCoreStoreConfigService $magentoCoreStoreConfigService,
    ) {
    }

    public function collectProduct(string $uid, $selectedOptions = [], bool $debug = false): array
    {
        $cacheKey = 'catalog_product_'.$uid;

        if ( ! empty($selectedOptions)) {
            $cacheKey .= '_'.implode('_', $selectedOptions);
        }

        if ($debug) {
            $this->redisAdapter->clear($cacheKey);
        }

        return $this->redisAdapter->get(
            $cacheKey,
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
                                    ...ProductType::fields($selectedOptions),
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

    public function collectProducts(array $uids): array
    {
        $products = [];
        foreach ($uids as $uid) {
            $products[] = $this->collectProduct($uid);
        }

        return $products ?? [];
    }

    public function collectHomeFeaturedProducts($debug = false)
    {
        $uid = base64_encode($this->magentoCoreStoreConfigService->getStoreConfigData('featured_category_id'));

        return $this->redisAdapter->get(
            'catalog_home_featured_products_'.$uid,
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