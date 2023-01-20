<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class OrderType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('status'),
            new Field('created_at'),
            new Field('increment_id'),
        ];
    }
}