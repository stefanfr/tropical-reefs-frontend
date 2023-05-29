<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;
use App\Service\GraphQL\Selection;

class CustomerType implements TypeInterface
{
    public static function fields(): array
    {
        return [
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
                    ...CustomerDetailType::fields(),
                ]
            ),
            (new Selection('wishlists')
            )->addChildFields(
                [
                    new Field('id'),
                    ...CustomerWishlistType::fields(),
                ]
            ),
        ];
    }
}