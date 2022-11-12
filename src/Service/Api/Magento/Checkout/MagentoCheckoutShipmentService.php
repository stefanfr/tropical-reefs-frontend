<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Input;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MagentoCheckoutShipmentService
{
    public function __construct(
        protected RedisAdapter              $redisAdapter,
        protected RequestStack              $requestStack,
        protected MageGraphQlClient         $mageGraphQlClient,
        protected MagentoCheckoutApiService $magentoCheckoutApiService,
    )
    {
    }

    public function saveShippingMethod(?array $selectedShippingMethod): false|array
    {
        $response = (new Request(
            (new Mutation('setShippingMethodsOnCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        (new InputField('shipping_methods')
                        )->addChildInputFields(
                            [
                                new InputField('carrier_code', $selectedShippingMethod['carrier_code']),
                                new InputField('method_code', $selectedShippingMethod['method_code']),
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
}