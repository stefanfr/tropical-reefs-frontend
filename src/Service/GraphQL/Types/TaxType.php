<?php

namespace App\Service\GraphQL\Types;

class TaxType implements TypeInterface
{
    public static function fields(): array
    {
        return MoneyType::fields();
    }
}