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

class MagentoCheckoutAddressApiService
{
    public function __construct(
        protected RedisAdapter $redisAdapter,
        protected RequestStack $requestStack,
        protected MageGraphQlClient $mageGraphQlClient,
        protected MagentoCheckoutApiService $magentoCheckoutApiService,
    ) {
    }

    public function setCustomerEmail(string $customerEmail): array|bool
    {
        $response = (new Request(
            (new Mutation('setGuestEmailOnCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        new InputField('email', $customerEmail),
                    ]
                ),
            )->addField(
                (new Field('cart')
                )->addChildField(
                    new Field('email')
                ),
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['errors'] ?? false;
    }

    public function saveShippingAddressDetails(array $addressDetails): array|bool
    {
        $response = (new Request(
            (new Mutation('setShippingAddressesOnCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        (new InputField('shipping_addresses')
                        )->addChildInputField(
                            (new InputObject('address')
                            )->addInputFields(
                                [
                                    new InputField('firstname', $addressDetails['firstname']),
                                    new InputField('lastname', $addressDetails['lastname']),
                                    new InputField('company', $addressDetails['company']),
                                    new InputField('telephone', $addressDetails['telephone']),
                                    new InputField('city', $addressDetails['city']),
                                    new InputField('postcode', $addressDetails['postcode']),
                                    new InputField('street', $addressDetails['street']),
                                    new InputField('country_code', $addressDetails['country_code']),
                                ]
                            ),
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

        return $response['errors'] ?? false;
    }

    public function saveBillingAddressDetails(array $addressDetails, bool $sameAsShipping = true): array|bool
    {
        $response = (new Request(
            $query = (new Mutation('setBillingAddressOnCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        (new InputObject('billing_address')
                        )->addInputFields(
                            [
                                (new InputObject('address')
                                )->addInputFields(
                                    [
                                        new InputField('firstname', $addressDetails['firstname']),
                                        new InputField('lastname', $addressDetails['lastname']),
                                        new InputField('company', $addressDetails['company']),
                                        new InputField('telephone', $addressDetails['telephone']),
                                        new InputField('city', $addressDetails['city']),
                                        new InputField('postcode', $addressDetails['postcode']),
                                        new InputField('street', $addressDetails['street']),
                                        new InputField('country_code', $addressDetails['country_code']),
                                    ]
                                ),
                                (new InputField('use_for_shipping', $sameAsShipping)),
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

        return $response['errors'] ?? false;
    }

    public function saveShippingAddressId(int $addressId): array|bool
    {
        $response = (new Request(
            (new Mutation('setBillingAddressOnCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        (new InputObject('billing_address')
                        )->addInputFields(
                            [
                                new InputField('customer_address_id', $addressId),
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

        return $response['errors'] ?? false;
    }

    public function saveBillingAddressId(int $addressId, bool $sameAsShipping = true): array|bool
    {
        $response = (new Request(
            (new Mutation('setBillingAddressOnCart')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('cart_id', $this->magentoCheckoutApiService->getQuoteMaskId()),
                        (new InputObject('billing_address')
                        )->addInputFields(
                            [
                                new InputField('customer_address_id', $addressId),
                                (new InputField('use_for_shipping', $sameAsShipping)),
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

        return $response['errors'] ?? false;
    }
}