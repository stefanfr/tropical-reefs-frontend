<?php

namespace App\Service\Api\Magento\Customer\Account;

use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\InputFieldEnum;
use App\Service\GraphQL\InputObject;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Service\GraphQL\Types\CustomerOrderType;
use App\Service\GraphQL\Types\CustomerType;

class MagentoCustomerAccountQueryService extends BaseMagentoService
{
    public function getCustomerData(bool $overviewPage = true): ?array
    {
        static $customerData = null;

        if ($customerData !== null) {
            return $customerData;
        }

        $selectedOptions = [
            (new InputObject('sort')
            )->addInputFields(
                [
                    new InputFieldEnum('sort_field', 'CREATED_AT'),
                    new InputFieldEnum('sort_direction', 'DESC'),
                ]
            ),
        ];

        if ($overviewPage) {
            $selectedOptions[] = new InputField('pageSize', 1);
        }

        $response = (new Request(
            (new Query('customer')
            )->addFields(
                [
                    ...CustomerType::fields(),
                    ...CustomerOrderType::fields($selectedOptions),
                ]
            ),
            $this->mageGraphQlClient
        ))->send();

        $response = $this->checkErrorsResponse($response, 'customer');

        return $customerData = $response['data']['customer'] ?? null;
    }
}