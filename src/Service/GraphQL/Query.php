<?php

namespace App\Service\GraphQL;

class Query
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
        $query = '{';
        $query .= $this->query;
        $query .= '(';

        foreach ($this->params ?? [] as $param) {
            $query .= $param->__toString();
        }
        $query .= ') {';

        foreach ($this->fields as $field) {
            $query .= $field->__toString() . ' ';
        }

        $query .= '}}';
        return $query;
    }
}