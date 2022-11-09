<?php

namespace App\Service\GraphQL;

class InputField
{
    public function __construct(
        protected string $name,
        protected mixed  $value = null,
        protected array  $childInputFields = [],
    )
    {
    }

    public function addChildInputField(InputField $inputField): static
    {
        $this->childInputFields[] = $inputField;

        return $this;
    }

    public function addChildInputFields(array $inputFields): static
    {
        foreach ($inputFields as $inputField) {
            $this->addChildInputField($inputField);
        }

        return $this;
    }

    public function __toString(): string
    {
        $inputField = $this->name;
        $inputField .= ': ';

        if (null !== $this->value) {
            $inputField .= '"' . $this->value . '"';
        } else {
            $inputField .= ' [ {';

            foreach ($this->childInputFields as $childInputField) {
                $inputField .= $childInputField->__toString() . ' ';
            }

            $inputField .= ' } ]';
        }

        return $inputField;
    }
}