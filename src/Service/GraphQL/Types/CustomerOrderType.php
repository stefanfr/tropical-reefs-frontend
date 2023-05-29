<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;
use App\Service\GraphQL\Selection;

class CustomerOrderType implements TypeInterface
{
    public static function fields(...$args): array
    {
        [$selectedOptions] = $args;

        return [
            (new Selection('orders', $selectedOptions)
            )->addChildFields(
                [
                    (new Field('items')
                    )->addChildFields(
                        [
                            ...OrderType::fields(),
                            (new Field('total')
                            )->addChildFields([
                                new Field('grand_total', MoneyType::fields()),
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
        ];
    }
}