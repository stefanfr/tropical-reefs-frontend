<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class CustomerWishlistType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('items_count'),
            (new Field('items')
            )->addChildFields(
                [
                    new Field('id'),
                    new Field('qty'),
                    new Field('added_at'),
                    new Field('description'),
                    (new Field('product')
                    )->addChildFields(
                        [
                            ...CustomerWishlistItemType::fields(),
                        ]
                    ),
                ]
            ),
        ];
    }
}