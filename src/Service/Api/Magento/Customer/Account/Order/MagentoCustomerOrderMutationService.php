<?php

namespace App\Service\Api\Magento\Customer\Account\Order;

use App\DataClass\Customer\Address\CustomerAddress;
use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Input;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\InputFieldEnum;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Request;

class MagentoCustomerOrderMutationService extends BaseMagentoService
{
    public function saveAddress(CustomerAddress $address): false|array
    {
        $mutation = 'createCustomerAddress';
        $parameters = [
            (new Input('input')
            )->addFields(
                [
                    new InputField('firstname', $address->getFirstname()),
                    new InputField('lastname', $address->getLastname()),
                    new InputField('company', $address->getCompany() ?? ''),
                    new InputField('street', [$address->getStreet(), $address->getHouseNr(), $address->getAdd()]),
                    new InputField('postcode', $address->getPostcode()),
                    new InputField('city', $address->getCity()),
                    new InputFieldEnum('country_code', $address->getCountryCode() ?? 'NL'),
                    new InputField('telephone', $address->getTelephone()),
                    new InputField('default_shipping', $address->isDefaultShipping()),
                    new InputField('default_billing', $address->isDefaultBilling()),
                ]
            ),
        ];

        if ($address->getId() !== null) {
            $mutation = 'updateCustomerAddress';
            $parameters[] = new InputField('id', $address->getId());
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