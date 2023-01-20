<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Input;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\InputObject;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Service\GraphQL\Types\AddressType;
use App\Service\GraphQL\Types\OrderItemType;
use App\Service\GraphQL\Types\OrderType;
use App\Service\GraphQL\Types\TotalsType;
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

    public function placeOrder(): string|array
    {
        $response = (new Request(
            (new Mutation('placeOrder')
            )->addParameter(
                (new Input('input')
                )->addField(
                    new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId())
                )
            )->addField(
                (new Field('order')
                )->addChildField(
                    new Field('order_number')
                )
            ),
            $this->mageGraphQlClient,
        ))->send();

        return $response['data']['placeOrder']['order']['order_number'] ?? $response;
    }

    public function startPayNlTransaction(string $orderId, string $returnUrl): string|array
    {
        $response = (new Request(
            (new Mutation('paynlStartTransaction')
            )->addParameters(
                [
                    new InputField('order_id', $orderId),
                    new InputField('return_url', $returnUrl),
                ]
            )->addField(
                (new Field('redirectUrl'))
            ),
            $this->mageGraphQlClient,
        ))->send();

        return $response['data']['paynlStartTransaction']['redirectUrl'] ?? $response;
    }

    public function finalizeOrder(string $payOrderId): array|bool
    {
        $response = (new Request(
            (new Mutation('paynlFinishTransaction')
            )->addParameters(
                [
                    new InputField('pay_order_id', $payOrderId),
                ]
            )->addFields(
                [
                    new Field('state'),
                    new Field('isSuccess'),
                    new Field('orderNumber'),
                ]
            ),
            $this->mageGraphQlClient,
        ))->send();

        return $response['data']['paynlFinishTransaction'] ?? false;
    }

    public function collectOrder(mixed $orderNumber): false|array
    {
        $response = (new Request(
            (new Query('salesOrder'))
                ->addParameter(
                    (new Parameter('orderNumber', $orderNumber))
                )->addFields(
                    [
                        ...OrderType::fields(),
                        new Field('totals', TotalsType::fields()),
                        new Field('items', OrderItemType::fields()),
                        new Field('shipping_address', AddressType::fields()),
                        new Field('billing_address', AddressType::fields()),
                    ]
                ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data']['salesOrder'] ?? false;
    }
}