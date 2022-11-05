<?php

namespace App\Service\GraphQL;

class Field
{
    public function __construct(
        protected string $fieldName,
        protected array  $childFields = []
    )
    {
    }

    public function addChildField(Fragment|Field $field): static
    {
        $this->childFields[] = $field;

        return $this;
    }

    public function addChildFields(array $childFields): static
    {
        foreach ($childFields as $childField) {
            $this->addChildField($childField);
        }

        return $this;
    }

    public function __toString(): string
    {
        $field = $this->fieldName;
        if (empty($this->childFields)) {
            return $field;
        }

        $field .= '{';

        foreach ($this->childFields as $childField) {
            $field .= $childField->__toString() . ' ';
        }

        $field .= '}';

        return $field;
    }
}