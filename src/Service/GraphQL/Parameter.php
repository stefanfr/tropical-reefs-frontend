<?php

namespace App\Service\GraphQL;

class Parameter
{
    public function __construct(
        protected string $field,
        protected mixed  $value
    )
    {
    }

    public function __toString(): string
    {
        return $this->field . ':' . $this->value;
    }
}