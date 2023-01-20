<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class TaxItemType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('rate'),
            new Field('title'),
            new Field('amount', MoneyType::fields()),
        ];
    }
}