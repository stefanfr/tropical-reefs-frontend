<?php

namespace App\Service\Api\Magento\Customer\Account\General;

use App\DataClass\Customer\CustomerAccount;
use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Request;

class MagentoCustomerAccountGeneralMutation extends BaseMagentoService
{
    public function saveSettings(CustomerAccount $customerAccount): false|array
    {

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
        )
        )->send();

        return $response['errors'] ?? false;
    }
}