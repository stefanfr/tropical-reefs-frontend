<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Fragment;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RequestStack;

class MagentoCheckoutCartApiService
{
    public function __construct(
        protected RedisAdapter              $redisAdapter,
        protected RequestStack              $requestStack,
        protected MageGraphQlClient         $mageGraphQlClient,
        protected MagentoCheckoutApiService $magentoCheckoutApiService,
    )
    {
    }

    public function addProductToCart(array $itemData)
    {
        $response = (new Request(
            (new Mutation('addProductsToCart',
                [
                    new Parameter('cartId', $this->magentoCheckoutApiService->getQuoteMaskId()),
                    (new Parameter('cartItems', null)
                    )->addFields(
                        [
                            new Parameter('sku', $itemData['sku']),
                            new Parameter('quantity', $itemData['qty']),
                            new Parameter('selected_options', $itemData['options'] ?? []),
                        ]
                    ),
                ]
            ))->addFields(
                [
                    (new Field('cart')
                    )->addChildFields(
                        [
                            new Field('total_quantity'),
                            (new Field('items')
                            )->addChildFields(
                                [
                                    new Field('quantity'),
                                    (new Field('product')
                                    )->addChildFields(
                                        [
                                            new Field('sku'),
                                            new Field('name'),
                                            (new Field('small_image')
                                            )->addChildField(
                                                new Field('url'),
                                            ),
                                        ]
                                    ),
                                ]
                            ),
                            (new Field('prices')
                            )->addChildFields(
                                [
                                    (new Field('discounts')
                                    )->addChildFields(
                                        [
                                            new Field('label'),
                                            (new Field('amount')
                                            )->addChildFields(
                                                [
                                                    new Field('value'),
                                                    new Field('currency'),
                                                ]
                                            ),
                                        ]
                                    ),
                                    (new Field('subtotal_including_tax')
                                    )->addChildFields(
                                        [
                                            new Field('value'),
                                            new Field('currency'),
                                        ]
                                    ),
                                    (new Field('grand_total')
                                    )->addChildFields(
                                        [
                                            new Field('value'),
                                            new Field('currency'),
                                        ]
                                    ),
                                ]
                            ),
                        ]
                    ),
                    (new Field('user_errors')
                    )->addChildFields(
                        [
                            new Field('code'),
                            new Field('message'),
                        ]
                    ),
                ]
            ),
            $this->mageGraphQlClient
        )
        )->send();

        return $response['data']['addProductsToCart'] ?? throw new BadRequestException('Something went wrong');
    }

    public function collectCart(): array
    {
        $response = (new Request(
            (new Query('cart')
            )->addParameter(
                new Parameter('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
            )->addFields(
                [
                    (new Field('items')
                    )->addChildFields(
                        [
                            new Field('id'),
                            new Field('quantity'),
                            (new Fragment('ConfigurableCartItem')
                            )->addFields(
                                [
                                    (new Field('configurable_options')
                                    )->addChildFields(
                                        [
                                            new Field('option_label'),
                                            new Field('value_label'),
                                        ]
                                    ),
                                ]
                            ),
                            (new Field('product')
                            )->addChildFields(
                                [
                                    new Field('name'),
                                    new Field('sku'),
                                    new Field('url_key'),
                                    (new Field('small_image')
                                    )->addChildField(
                                        new Field('url')
                                    ),
                                ]
                            ),
                            (new Field('errors')
                            )->addChildFields(
                                [
                                    new Field('code'),
                                    new Field('message'),
                                ]
                            ),
                            (new Field('prices')
                            )->addChildFields(
                                [
                                    (new Field('price')
                                    )->addChildFields(
                                        [
                                            new Field('value'),
                                            new Field('currency'),
                                        ]
                                    ),
                                    (new Field('row_total_including_tax')
                                    )->addChildFields(
                                        [
                                            new Field('value'),
                                            new Field('currency'),
                                        ]
                                    ),
                                    (new Field('total_item_discount')
                                    )->addChildFields(
                                        [
                                            new Field('value'),
                                            new Field('currency'),
                                        ]
                                    ),
                                ]
                            ),
                        ]
                    ),
                    (new Field('prices')
                    )->addChildFields(
                        [
                            (new Field('subtotal_including_tax')
                            )->addChildFields(
                                [
                                    new Field('value'),
                                    new Field('currency'),
                                ]
                            ),
                            (new Field('discounts')
                            )->addChildFields(
                                [
                                    new Field('label'),
                                    (new Field('amount')
                                    )->addChildFields(
                                        [
                                            new Field('value'),
                                            new Field('currency'),
                                        ]
                                    ),
                                ]
                            ),
                            (new Field('applied_taxes')
                            )->addChildFields(
                                [
                                    new Field('label'),
                                    (new Field('amount')
                                    )->addChildFields(
                                        [
                                            new Field('value'),
                                            new Field('currency'),
                                        ]
                                    ),
                                ]
                            ),
                            (new Field('grand_total')
                            )->addChildFields(
                                [
                                    new Field('value'),
                                    new Field('currency'),
                                ]
                            ),
                        ]
                    ),
                ]
            ),
            $this->mageGraphQlClient
        )
        )->send();

        return $response['data']['cart'] ?? dd($response['errors']);// throw new BadRequestException('Failed to load cart');
    }
}