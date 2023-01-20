<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class ImageType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('url'),
            new Field('label'),
        ];
    }
}