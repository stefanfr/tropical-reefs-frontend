<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class OrderType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('increment_id'),
            new Field('created_at'),
            new Field('grand_total'),
            new Field('carrier'),
            new Field('items', OrderItemType::fields()),
        ];
    }
}