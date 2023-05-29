<?php

namespace App\Service\GraphQL;

class InputObject
{
    public function __construct(
        protected null|string $name = null,
        protected array       $childInputFields = [],
    )
    {
    }

    public function addInputField(InputField|InputObject|InputFieldEnum $inputField): static
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
        $inputField = ' ';
        if (null !== $this->name) {
            $inputField = $this->name;
            $inputField .= ': ';
        }
        $inputField .= '{';

        foreach ($this->childInputFields as $childInputField) {
            $inputField .= $childInputField->__toString() . ' ';
        }

        $inputField .= ' }';

        return $inputField;
    }
}