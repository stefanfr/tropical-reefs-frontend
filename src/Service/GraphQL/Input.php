<?php

namespace App\Service\GraphQL;

class Input
{
    public function __construct(
        protected string $name,
        protected array  $fields = []
    )
    {
    }

    public function addField(InputField|InputObject|InputFieldEnum $field): static
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
        $input = $this->name;
        $input .= ': {';

        foreach ($this->fields as $key => $field) {
            if ($key) {
                $input .= ',';
            }
            $input .= $field;
        }
        $input .= '}';
        return $input;
    }
}