<?php

namespace App\Service\Api\Magento\Customer\Account;

use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Service\GraphQL\Selection;
use App\Service\GraphQL\Types\AddressType;
use App\Service\GraphQL\Types\MoneyType;
use App\Service\GraphQL\Types\OrderItemType;
use App\Service\GraphQL\Types\OrderType;
use App\Service\GraphQL\Types\TotalsType;

class MagentoCustomerAccountQueryService extends BaseMagentoService
{
    public static function getCustomerDetailFields(): array
    {
        return [
            new Field('firstname'),
            new Field('lastname'),
            new Field('company'),
            new Field('street'),
            new Field('postcode'),
            new Field('city'),
            new Field('country_code'),
            new Field('telephone'),
        ];
    }

    public function getCustomerData(): array|null
    {
        static $customerData = null;

        if ($customerData !== null) {
            return $customerData;
        }

        $selectedOptions = [
            new InputField('pageSize', 100),
        ];
        $response = (new Request(
            (new Query('customer')
            )->addFields(
                [
                    new Field('email'),
                    new Field('firstname'),
                    new Field('lastname'),
                    new Field('is_subscribed'),
                    new Field('default_shipping'),
                    new Field('default_billing'),
                    (new Field('addresses')
                    )->addChildFields(
                        [
                            new Field('id'),
                            ...self::getCustomerDetailFields(),
                        ]
                    ),
                    (new Selection('orders', $selectedOptions)
                    )->addChildFields(
                        [
                            (new Field('items')
                            )->addChildFields(
                                [
                                    ...OrderType::fields(),
                                    (new Field('total')
                                    )->addChildFields([
                                        new Field('grand_total', MoneyType::fields())
                                    ]),
                                ]
                            ),
                            (new Field('page_info')
                            )->addChildFields(
                                [
                                    new Field('current_page'),
                                    new Field('page_size'),
                                    new Field('total_pages'),
                                ]
                            ),
                        ]
                    ),
                ]
            ),
            $this->mageGraphQlClient
        ))->send();

        $response = $this->checkErrorsResponse($response, 'customer');

        return $customerData = $response['data']['customer'] ?? null;
    }
}