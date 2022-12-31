<?php

namespace App\Service\Api\Magento\Customer\Account\Order;

use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;

class MagentoCustomerOrderQueryService extends BaseMagentoService
{
    public function collectCustomerOrders(): false|array
    {
        $response = (new Request(
            (new Query('customerOrders')
            )->addField(
                (new Field('items')
                )->addChildFields(
                    [
                        new Field('id'),
                        new Field('order_number'),
                        new Field('created_at'),
                        new Field('grand_total'),
                        new Field('status'),
                    ]
                )
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data']['customerOrders']['items'] ?? false;
    }
}