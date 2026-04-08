<?php

declare(strict_types=1);

namespace App\Application\Filtering;

final readonly class Filter
{
    public string $column;

    /**
     * @param  string|list<string>  $value
     */
    public function __construct(
        string $column,
        public FilterOperator $operator = FilterOperator::Equals,
        public string|array $value = '',
    ) {
        if ($operator === FilterOperator::Search) {
            $this->column = '';
        } else {
            $this->column = $column !== '' && preg_match('/^[a-z_]+$/D', $column) === 1 ? $column : '';
        }
    }
}
