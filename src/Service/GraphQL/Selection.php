<?php

namespace App\Service\GraphQL;

class Selection
{
    public function __construct(
        protected string $fieldName,
        protected array  $params = [],
        protected array  $childFields = []
    )
    {
    }

    public function addParameter(Parameter $parameters): static
    {
        $this->params[] = $parameters;

        return $this;
    }

    public function addParameters(array $parameters): static
    {
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }

        return $this;
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

        if ( ! empty($this->params)) {
            $field .= '(';
            foreach ($this->params as $param) {
                $field .= $param->__toString();
            }
            $field .= ')';
        }

        $field .= '{';

        foreach ($this->childFields as $childField) {
            $field .= $childField->__toString() . ' ';
        }

        $field .= '}';

        return $field;
    }
}