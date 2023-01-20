<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class MoneyType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('value'),
            new Field('currency'),
        ];
    }
}