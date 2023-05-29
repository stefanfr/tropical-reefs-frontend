<?php

namespace App\Service\Api\Magento\Customer\Account\Address;

use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Input;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\InputFieldEnum;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Request;

class MagentoCustomerAddressMutationService extends BaseMagentoService
{
    public function saveAddress(array $address, ?int $addressId = null): false|array
    {
        $mutation = 'createCustomerAddress';
        $parameters = [
            (new Input('input')
            )->addFields(
                [
                    new InputField('firstname', $address['firstname']),
                    new InputField('lastname', $address['lastname']),
                    new InputField('company', $address['company'] ?? ''),
                    new InputField('street', $address['street']),
                    new InputField('postcode', $address['postcode']),
                    new InputField('city', $address['city']),
                    new InputFieldEnum('country_code', $address['countryCode']),
                    new InputField('telephone', $address['telephone']),
                    new InputField('default_shipping', $address['isDefaultShipping']),
                    new InputField('default_billing', $address['isDefaultBilling']),
                ]
            ),
        ];

        if (null !== $addressId) {
            $mutation = 'updateCustomerAddress';
            $parameters[] = new InputField('id', $addressId);
        }

        $response = (new Request(
            (new Mutation($mutation)
            )->addParameters(
                $parameters
            )->addField(
                new Field('id')
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['errors'] ?? false;
    }

    public function deleteAddress(int $addressId)
    {
        $response = (new Request(
            (new Mutation('deleteCustomerAddress')
            )->addParameter(
                new InputField('id', $addressId)
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['errors'] ?? false;
    }
}