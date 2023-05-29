<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;
use App\Service\GraphQL\Fragment;

class CustomerWishlistItemType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('uid'),
            new Field('type_id'),
            new Field('url_key'),
            new Field('name'),
            new Field('sku'),
            (new Field('price_range')
            )->addChildField(
                (new Field('minimum_price')
                )->addChildFields(
                    [
                        (new Field('final_price'))
                            ->addChildFields(
                                [
                                    new Field('value'),
                                    new Field('currency'),
                                ]
                            ),
                        (new Field('regular_price'))
                            ->addChildFields(
                                [
                                    new Field('value'),
                                    new Field('currency'),
                                ]
                            ),
                        (new Field('discount'))
                            ->addChildFields(
                                [
                                    new Field('amount_off'),
                                    new Field('percent_off'),
                                ]
                            ),
                    ]
                )
            ),
            (new Field('small_image')
            )->addChildFields(
                [
                    new Field('url'),
                    new Field('label'),
                ]
            ),
            (new Fragment('ConfigurableProduct')
            )->addFields(
                [
                    (new Field('variants')
                    )->addChildFields(
                        [
                            (new Field('product')
                            )->addChildFields(
                                [
                                    (new Field('name')),
                                    (new Field('sku')),
                                    (new Field('size')),
                                ]
                            ),
                        ]
                    ),
                ]
            ),
        ];
    }
}