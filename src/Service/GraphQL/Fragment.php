<?php

namespace App\Service\GraphQL;

class Fragment
{
    public function __construct(
        protected string $fragmentName,
        protected array  $fields = []
    )
    {
    }

    public function addField(Field $field): static
    {
        $this->fields[] = $field;

        return $this;
    }

    public function addFields(array $fields): static
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    public function __toString(): string
    {
        $fragment = '... on ';
        $fragment .= $this->fragmentName;
        $fragment .= '{';

        foreach ($this->fields as $field) {
            $fragment .= $field->__toString() . ' ';
        }

        $fragment .= '}';

        return $fragment;
    }
}