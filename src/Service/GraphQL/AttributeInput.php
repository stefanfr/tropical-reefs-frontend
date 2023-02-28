<?php

namespace App\Service\GraphQL;

class AttributeInput
{
    public function __construct(
        protected string $fieldName = 'attributes',
        protected array  $attributes = [],
    )
    {
    }

    public function addAttribute($field): static
    {
        $this->attributes[] = $field;

        return $this;
    }

    public function addAttributes(array $fields): static
    {
        foreach ($fields as $field) {
            $this->addAttribute($field);
        }

        return $this;
    }

    public function __toString(): string
    {
        $field = $this->fieldName;
        $field .= ': [' . implode('', $this->attributes) . ']';

        return $field;
    }
}