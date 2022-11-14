<?php

namespace App\Service\GraphQL;

class Field
{
    public function __construct(
        protected string   $fieldName,
        protected array    $childFields = [],
        protected ?Filters $filters = null
    )
    {
    }

    public function addChildField(Fragment|Field|Parameter $field): static
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

    public function addFilter(Filter $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function addFilters(array $filters): static
    {
        foreach ($filters as $filter) {
            $this->addFilters($filter);
        }

        return $this;
    }

    public function __toString(): string
    {
        $field = $this->fieldName;

        if (null !== $this->filters) {
            $field .= '(' . $this->filters . ')';
        }

        if (empty($this->childFields)) {
            return $field;
        }

        $field .= '{';

        foreach ($this->childFields as $childField) {
            $field .= $childField->__toString() . ' ';
        }

        $field .= '}';

        return $field;
    }
}