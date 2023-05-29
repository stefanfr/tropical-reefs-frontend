<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Fragment;
use App\Service\GraphQL\Input;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
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

    public function collectFullCart(bool $fresh = false)
    {
        $session = null;
        try {
            $session = $this->requestStack->getSession();
        } catch (SessionNotFoundException $exception) {
        }

        if ($session?->has('customerToken')) {
            $query = (new Query('customerCart'));
        } else {
            $query = (new Query('cart')
            )->addParameter(
                new Parameter('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId($fresh)),
            );
        }

        $response = (new Request(
            $query->addFields(
                [
                    new Field('id'),
                    new Field('email'),
                    new Field('total_quantity'),
                    (new Field('items')
                    )->addChildFields(
                        [
                            new Field('uid'),
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
                                    (new Field('price_including_tax')
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
                    (new Field('billing_address')
                    )->addChildFields(
                        [
                            new Field('firstname'),
                            new Field('lastname'),
                            new Field('street'),
                            new Field('postcode'),
                            new Field('city'),
                            new Field('telephone'),
                            (new Field('country')
                            )->addChildField(
                                new Field('code')
                            ),
                        ]
                    ),
                    (new Field('shipping_addresses')
                    )->addChildFields(
                        [
                            new Field('firstname'),
                            new Field('lastname'),
                            new Field('street'),
                            new Field('postcode'),
                            new Field('city'),
                            new Field('telephone'),
                            (new Field('country')
                            )->addChildField(
                                new Field('code')
                            ),
                            (new Field('available_shipping_methods')
                            )->addChildFields(
                                [
                                    (new Field('amount')
                                    )->addChildField(
                                        new Field('value')
                                    ),
                                    (new Field('price_excl_tax')
                                    )->addChildField(
                                        new Field('value')
                                    ),
                                    (new Field('price_incl_tax')
                                    )->addChildField(
                                        new Field('value')
                                    ),
                                    new Field('available'),
                                    new Field('carrier_code'),
                                    new Field('carrier_title'),
                                    new Field('error_message'),
                                    new Field('method_code'),
                                    new Field('method_title'),
                                ]
                            ),
                            (new Field('selected_shipping_method')
                            )->addChildFields(
                                [
                                    (new Field('price_incl_tax')
                                    )->addChildFields(
                                        [
                                            new Field('value'),
                                            new Field('currency'),
                                        ]
                                    ),
                                    new Field('carrier_code'),
                                    new Field('carrier_title'),
                                    new Field('method_code'),
                                    new Field('method_title'),
                                ]
                            ),
                        ]
                    ),
                    (new Field('available_payment_methods')
                    )->addChildFields(
                        [
                            new Field('code'),
                            new Field('title'),
                        ]
                    ),
                    (new Field('selected_payment_method')
                    )->addChildFields(
                        [
                            new Field('code'),
                            new Field('title'),
                        ]
                    ),
                    (new Field('applied_coupons')
                    )->addChildFields(
                        [
                            new Field('code'),
                        ]
                    ),
                ]
            ),
            $this->mageGraphQlClient
        ))->send();

        try {
            $cartData = $response['data']['cart'] ?? $response['data']['customerCart'];
        } catch (Exception $e) {
            if ($session?->has('customerToken')) {
                $session->remove('customerToken');
            }
            return $this->collectFullCart(true);
        }

        if (isset($cartData['total_quantity'])) {
            $session->set('checkout_cart_item_count', $cartData['total_quantity']);
        }

        return $cartData;
    }

    public function addProductToCart(array $itemData, bool $fresh = false)
    {
        $request = new Request(
            (new Mutation('addProductsToCart',
                [
                    new Parameter('cartId', $this->magentoCheckoutApiService->getQuoteMaskId($fresh)),
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
                                    new Field('uid'),
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
        );

        $response = $request->send();

        if (isset($response['data']['addProductsToCart']['cart']['total_quantity'])) {
            try {
                $session = $this->requestStack->getSession();
                $session->set('checkout_cart_item_count', $response['data']['addProductsToCart']['cart']['total_quantity']);
            } catch (SessionNotFoundException $exception) {

            }
        }

        if ($response['errors'] ?? false) {
            $this->addProductToCart($itemData, true);
        }

        return $response['data']['addProductsToCart'] ?? throw new BadRequestException('Something went wrong');
    }

    public function updateItemQty(string $uid, int $selectedQty): array
    {
        $response = (new Request(
            (new Mutation('updateCartItems')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        (new InputField('cart_items')
                        )->addChildInputFields(
                            [
                                new InputField('cart_item_uid', $uid),
                                new InputField('quantity', $selectedQty),
                            ]
                        ),
                    ]
                )
            )->addFields(
                [
                    (new Field('cart')
                    )->addChildField(
                        new Field('id')
                    ),
                ]
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data'] ?? throw new BadRequestException('Failed to load cart');
    }

    public function deleteItem(string $uid): array
    {
        $response = (new Request(
            (new Mutation('removeItemFromCart')
            )->addParameter(
                (new Parameter('input', null)
                )->addFields(
                    [
                        new Parameter('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        new Parameter('cart_item_uid', $uid),
                    ]
                )
            )->addFields(
                [
                    (new Field('cart')
                    )->addChildField(
                        new Field('id')
                    ),
                ]
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data'] ?? throw new BadRequestException('Failed to load cart');
    }

    public function formatTotals(array $cart): array
    {
        $cartTotals = [];
        $prices = $cart['prices'];

        if ($cart['shipping_addresses'][0]['selected_shipping_method'] ?? false) {
            $shippingMethod = $cart['shipping_addresses'][0]['selected_shipping_method'];
            $shippingCosts = [
                'label' => 'Shipping ( ' . $shippingMethod['carrier_title'] . ' )',
                'value' => $shippingMethod['price_incl_tax']['value'],
            ];
        }

        $prices = array_filter($prices, static function ($price) {
            return null !== $price;
        });

        foreach ($prices as $key => $price) {
            if ($key === 'applied_taxes' && ! empty($shippingCosts)) {
                $cartTotals[] = $shippingCosts;
            }
            if (array_key_exists('value', $price)) {
                $cartTotals[] = [
                    'label' => ucfirst(str_replace('_', ' ', $key)),
                    'value' => $price['value'],
                ];

                continue;
            }

            foreach ($price ?? [] as $taxRow) {
                $cartTotals[] = [
                    'label' => $taxRow['label'],
                    'value' => $taxRow['amount']['value'],
                ];
            }
        }

        return $cartTotals;
    }
}