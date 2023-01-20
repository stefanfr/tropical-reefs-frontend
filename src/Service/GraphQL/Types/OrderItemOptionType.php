<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class OrderItemOptionType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('label'),
            new Field('value'),
        ];
    }
}