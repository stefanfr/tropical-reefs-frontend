<?php

namespace App\Service\GraphQL;

class Filters
{
    public function __construct(
        protected string $key = 'filters',
        protected array  $filters = []
    )
    {
    }

    public function addFilter(Filter $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function addFilters(array $filters): string
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    public function __toString(): string
    {
        $filters = $this->key . ': {';
        foreach ($this->filters as $key => $filter) {
            if ($key) {
                $filters .= ' ';
            }
            $filters .= $filter->__toString();
        }

        $filters .= '}';

        return $filters;
    }
}