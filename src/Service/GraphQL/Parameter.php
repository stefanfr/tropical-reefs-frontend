<?php

namespace App\Service\GraphQL;

class Parameter
{
    public function __construct(
        protected string $field,
        protected mixed  $value,
        protected array  $fields = [],
    )
    {
    }

    public function addField(Field|Parameter $field): static
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
        $query = $this->field;

        if (null !== $this->value) {
            if (is_array($this->value)) {
                $query .= ': [';
                foreach ($this->value as $key => $value) {
                    if ($key > 0) {
                        $query .= ', ';
                    }
                    $query .= '"' . $value . '"';
                }
                $query .= ']';
            } else {
                $query .= ': "' . $this->value . '"';
            }
        }

        if ($this->fields) {
            $query .= ': {';

            foreach ($this->fields as $field) {
                $query .= $field->__toString() . ' ';
            }

            $query .= '}';
        }

        return $query . ' ';
    }
}