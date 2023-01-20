<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class AddressType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('firstname'),
            new Field('lastname'),
            new Field('street'),
            new Field('postcode'),
            new Field('city'),
            new Field('country_code'),
            new Field('telephone'),
        ];
    }
}