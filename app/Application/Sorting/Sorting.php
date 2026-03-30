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
        $this->column = $column !== '' ? $column : 'id';
    }
}
