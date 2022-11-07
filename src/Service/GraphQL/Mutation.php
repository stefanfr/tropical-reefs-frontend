<?php

namespace App\Service\GraphQL;

class Mutation
{
    public function __construct(
        protected string $query,
        protected array  $params = [],
        protected array  $fields = [],
    )
    {
    }

    public function addParameter(Parameter|Filters $parameter): static
    {
        $this->params[] = $parameter;

        return $this;
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
        $mutation = 'mutation {';
        $mutation .= $this->query;
        if ($this->params) {
            $mutation .= '(';

            foreach ($this->params as $param) {
                $mutation .= $param->__toString();
            }
            $mutation .= ')';
        }

        if ($this->fields) {
            $mutation .= ' {';

            foreach ($this->fields as $field) {
                $mutation .= $field->__toString() . ' ';
            }

            $mutation .= '}';
        }

        $mutation .= '}';

        return $mutation;
    }
}