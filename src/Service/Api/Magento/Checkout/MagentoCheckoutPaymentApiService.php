<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Input;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\InputObject;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MagentoCheckoutPaymentApiService
{
    public function __construct(
        protected RedisAdapter              $redisAdapter,
        protected RequestStack              $requestStack,
        protected MageGraphQlClient         $mageGraphQlClient,
        protected MagentoCheckoutApiService $magentoCheckoutApiService,
    )
    {
    }

    public function savePaymentMethod(string $methodCode): false|array
    {
        $response = (new Request(
            (new Mutation('setPaymentMethodOnCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        (new InputObject('payment_method')
                        )->addInputFields(
                            [
                                new InputField('code', $methodCode),
                            ]
                        ),
                    ]
                )
            )->addField(
                (new Field('cart')
                )->addChildField(
                    new Field('id')
                )
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['errors'] ?? false;
    }

    public function removeCoupon(): array
    {
        $response = (new Request(
            (new Mutation('removeCouponFromCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                    ]
                )
            )->addField(
                (new Field('cart')
                )->addChildFields(
                    [
                        new Field('id'),
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
                )
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data']['removeCouponFromCart']['cart'] ?? $response;
    }

    public function applyCoupon(string $couponCode): array
    {
        $response = (new Request(
            (new Mutation('applyCouponToCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        new InputField('coupon_code', $couponCode),
                    ]
                )
            )->addField(
                (new Field('cart')
                )->addChildFields(
                    [
                        new Field('id'),
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
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data']['applyCouponToCart']['cart'] ?? $response;
    }
}