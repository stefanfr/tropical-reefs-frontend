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

    public function addParameter(AttributeInput|Parameter|Filters $parameter): static
    {
        $this->params[] = $parameter;

        return $this;
    }

    public function addField(Field|Selection $field): static
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
        $query = 'query {';
        $query .= $this->query;
        if ($this->params) {
            $query .= '(';

            foreach ($this->params as $param) {
                $query .= $param->__toString();
            }
            $query .= ')';
        }

        if ($this->fields) {
            $query .= ' {';

            foreach ($this->fields as $field) {
                $query .= $field->__toString() . ' ';
            }

            $query .= '}';
        }

        $query .= '}';

        return $query;
    }
}