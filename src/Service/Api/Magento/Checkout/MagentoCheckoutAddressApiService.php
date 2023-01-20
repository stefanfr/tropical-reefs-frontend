<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\DataClass\Checkout\Address\Address;
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
        protected RedisAdapter              $redisAdapter,
        protected RequestStack              $requestStack,
        protected MageGraphQlClient         $mageGraphQlClient,
        protected MagentoCheckoutApiService $magentoCheckoutApiService,
    )
    {
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

    public function saveShippingAddressDetails(Address $addressDetails): array|bool
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
                                    new InputField('firstname', $addressDetails->getFirstname()),
                                    new InputField('lastname', $addressDetails->getLastname()),
                                    new InputField('company', $addressDetails->getCompany()),
                                    new InputField('telephone', $addressDetails->getPhone()),
                                    new InputField('city', $addressDetails->getCity()),
                                    new InputField('postcode', $addressDetails->getPostcode()),
                                    new InputField('street', [$addressDetails->getStreet(), $addressDetails->getHouseNr() . ' ' . $addressDetails->getAdd()]),
                                    new InputField('country_code', $addressDetails->getCountryCode() ?? 'NL'),
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

    public function saveBillingAddressDetails(Address $addressDetails, bool $sameAsShipping = true): array|bool
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
                                        new InputField('firstname', $addressDetails->getFirstname()),
                                        new InputField('lastname', $addressDetails->getLastname()),
                                        new InputField('company', $addressDetails->getCompany() ?? ''),
                                        new InputField('telephone', $addressDetails->getPhone()),
                                        new InputField('city', $addressDetails->getCity()),
                                        new InputField('postcode', $addressDetails->getPostcode()),
                                        new InputField('street', [$addressDetails->getStreet(), $addressDetails->getHouseNr() . ' ' . $addressDetails->getAdd()]),
                                        new InputField('country_code', $addressDetails->getCountryCode() ?? 'NL'),
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