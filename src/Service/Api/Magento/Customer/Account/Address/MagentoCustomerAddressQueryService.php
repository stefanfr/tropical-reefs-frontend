<?php

namespace App\Service\Api\Magento\Customer\Account\Address;

use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;

class MagentoCustomerAddressQueryService extends BaseMagentoService
{
    public function collectCustomerAddresses(): array
    {
        $response = (new Request(
            (new Query('customer')
            )->addField(
                (new Field('addresses')
                )->addChildFields(
                    [
                        new Field('id'),
                        new Field('firstname'),
                        new Field('lastname'),
                        new Field('company'),
                        new Field('street'),
                        new Field('postcode'),
                        new Field('city'),
                        new Field('country_code'),
                        new Field('telephone'),
                    ]
                ),
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data']['customer']['addresses'] ?? [];
    }
}