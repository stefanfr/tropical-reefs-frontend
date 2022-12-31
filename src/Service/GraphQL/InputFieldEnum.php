<?php

namespace App\Service\GraphQL;

class InputFieldEnum
{
    public function __construct(
        protected string $name,
        protected ?string  $value = null,
    )
    {
    }
    public function __toString(): string
    {
        $inputField = ' ' . $this->name;
        $inputField .= ': ';

        if (null !== $this->value) {
            $inputField .= $this->value;

            return $inputField;
        }

        return $inputField;
    }
}