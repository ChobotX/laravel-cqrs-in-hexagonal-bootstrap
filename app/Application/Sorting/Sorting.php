<?php

declare(strict_types=1);

namespace App\Application\Sorting;

final readonly class Sorting
{
    public string $column;

    public function __construct(
        string $column,
        public SortDirection $direction = SortDirection::Asc,
    ) {
        $this->column = $column !== '' && preg_match('/^[a-z_]+$/', $column) === 1 ? $column : 'id';
    }
}
