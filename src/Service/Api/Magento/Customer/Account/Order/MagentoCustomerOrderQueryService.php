<?php

namespace App\Service\Api\Magento\Customer\Account\Order;

use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Service\GraphQL\Types\AddressType;
use App\Service\GraphQL\Types\OrderItemType;
use App\Service\GraphQL\Types\OrderType;
use App\Service\GraphQL\Types\TotalsType;

class MagentoCustomerOrderQueryService extends BaseMagentoService
{
    public function collectCustomerOrder(string $orderNumber): false|array
    {
        $response = (new Request(
            (new Query('salesOrder'))
                ->addParameter(
                    (new Parameter('orderNumber', $orderNumber))
                )->addFields(
                    [
                        ...OrderType::fields(),
                        new Field('totals', TotalsType::fields()),
                        new Field('items', OrderItemType::fields()),
                        new Field('shipping_address', AddressType::fields()),
                        new Field('billing_address', AddressType::fields()),
                    ]
                ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data']['salesOrder'] ?? false;
    }
}