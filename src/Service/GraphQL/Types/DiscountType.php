<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class DiscountType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('amount', MoneyType::fields()),
        ];
    }
}