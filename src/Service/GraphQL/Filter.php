<?php

namespace App\Service\GraphQL;

use JsonException;

class Filter
{
    public function __construct(
        protected string $field,
        protected array  $operators = [],
    )
    {
    }

    public function addOperator(string $operator, mixed $value): static
    {
        $this->operators[$operator] = $value;

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        $filter = $this->field . ': ';

        foreach ($this->operators as $operator => $value) {
            $filter .= '{' . $operator . ': ' . json_encode($value, JSON_THROW_ON_ERROR) . '}';
        }

        return $filter;
    }
}