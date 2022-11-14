<?php

namespace App\Service\GraphQL;

class InputObject
{
    public function __construct(
        protected string $name,
        protected array  $childInputFields = [],
    )
    {
    }

    public function addInputField(InputField|InputObject $inputField): static
    {
        $this->childInputFields[] = $inputField;

        return $this;
    }

    public function addInputFields(array $inputFields): static
    {
        foreach ($inputFields as $inputField) {
            $this->addInputField($inputField);
        }

        return $this;
    }

    public function __toString(): string
    {
        $inputField = ' ' . $this->name;
        $inputField .= ': {';

        foreach ($this->childInputFields as $childInputField) {
            $inputField .= $childInputField->__toString() . ' ';
        }

        $inputField .= ' }';

        return $inputField;
    }
}