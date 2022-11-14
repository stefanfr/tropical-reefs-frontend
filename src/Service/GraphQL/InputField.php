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

    public function addChildInputField(InputField|InputObject $inputField): static
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
        $inputField = ' ' . $this->name;
        $inputField .= ': ';

        if (null !== $this->value) {
            if (is_array($this->value)) {
                $inputField .= ' [';

                foreach ($this->value as $key => $childInputField) {
                    if ($key) {
                        $inputField .= ", ";
                    }
                    $inputField .= '"' . $childInputField . '"';
                }

                $inputField .= ' ]';
                return $inputField;
            }

            if (is_bool($this->value)) {
                $inputField .= $this->value ? 'true' : 'false';
            } else if (is_int($this->value) || in_array($this->value, ['DESC', 'ASC'])) {
                $inputField .= $this->value;
            } else {
                $inputField .= '"' . $this->value . '"';
            }

            return $inputField;
        }

        $inputField .= ' [ {';

        foreach ($this->childInputFields as $childInputField) {
            $inputField .= $childInputField->__toString() . ' ';
        }

        $inputField .= ' } ]';

        return $inputField;
    }
}