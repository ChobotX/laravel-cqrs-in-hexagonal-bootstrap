<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Application\Sorting\SortableQuery;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Domain\Registry\Contract\Entity\Entry;

/**
 * @implements Query<PaginatedResult<Entry>>
 */
#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesQuery implements FilterableQuery, Query, SortableQuery
{
    public const int DEFAULT_PER_PAGE = 15;

    /**
     * @param  list<Filter>  $filters
     * @param  list<Sorting>  $sortings
     */
    public function __construct(
        public string $definitionId,
        public int $page = 1,
        public int $perPage = self::DEFAULT_PER_PAGE,
        private array $filters = [],
        private array $sortings = [],
    ) {}

    /** @param list<Filter> $filters */
    public function withFilters(array $filters): static
    {
        return new self($this->definitionId, $this->page, $this->perPage, $filters, $this->sortings);
    }

    /** @return list<Filter> */
    public function filters(): array
    {
        return $this->filters;
    }

    /** @param list<Sorting> $sortings */
    public function withSorting(array $sortings): static
    {
        return new self($this->definitionId, $this->page, $this->perPage, $this->filters, $sortings);
    }

    /** @return list<Sorting> */
    public function sorting(): array
    {
        return $this->sortings;
    }
}
