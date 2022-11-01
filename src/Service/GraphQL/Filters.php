<?php

namespace App\Service\GraphQL;

class Filters
{
    public function __construct(
        protected array $filters = []
    )
    {
    }

    public function addFilter(Filter $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function __toString(): string
    {
        $filters = 'filters: {';
        foreach ($this->filters as $filter) {
            $filters .= $filter->__toString();
        }

        $filters .= '}';

        return $filters;
    }
}