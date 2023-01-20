<?php

namespace App\Service\GraphQL\Types;

interface TypeInterface
{
    public static function fields(): array;
}