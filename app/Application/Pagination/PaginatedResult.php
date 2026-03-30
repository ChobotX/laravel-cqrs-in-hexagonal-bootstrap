<?php

declare(strict_types=1);

namespace App\Application\Pagination;

/**
 * @template T
 */
final readonly class PaginatedResult
{
    /**
     * @param  list<T>  $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public Pagination $pagination,
    ) {}

    public function totalPages(): int
    {
        return max(1, (int) ceil($this->total / $this->pagination->perPage));
    }

    public function hasNextPage(): bool
    {
        return $this->pagination->page < $this->totalPages();
    }

    public function hasPreviousPage(): bool
    {
        return $this->pagination->page > 1;
    }
}
