<?php

namespace App\Service\Api\Magento\Customer\Account\General;

use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Input;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Request;

class MagentoCustomerAccountGeneralMutationService extends BaseMagentoService
{
    public function saveSettings(array $customerAccount): false|array
    {
        $response = (new Request(
            (new Mutation('updateCustomer')
            )->addParameters(
                [
                    (new Input('input')
                    )->addFields(
                        [
                            new InputField('firstname', $customerAccount['firstname']),
                            new InputField('lastname', $customerAccount['lastname']),
                            new InputField('email', $customerAccount['email']),
                        ]
                    )
                ]
            )->addFields(
                [
                    (new Field('customer')
                    )->addChildFields(
                        [
                            new Field('firstname')
                        ]
                    ),
                ]
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['errors'] ?? false;
    }

    public function savePassword(string $currentPassword, string $newPassword): false|array
    {
        $response = (new Request(
            (new Mutation('changeCustomerPassword')
            )->addParameters(
                [
                    new InputField('currentPassword', $currentPassword),
                    new InputField('newPassword', $newPassword),
                ]
            )->addFields(
                [
                    new Field('id'),
                    new Field('email'),
                ]
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['errors'] ?? false;
    }
}