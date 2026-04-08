<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterableQuery;
use App\Application\Pagination\PaginableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortableQuery;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\Entity\Role;

/**
 * @implements Query<PaginatedResult<Role>>
 */
#[RequiresPermission('users.roles.read')]
final readonly class ListRolesQuery implements FilterableQuery, PaginableQuery, Query, SortableQuery
{
    /**
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     */
    public function __construct(
        private ?Pagination $pagination = null,
        private array $sortings = [],
        private array $filters = [],
    ) {}

    public function withPagination(Pagination $pagination): static
    {
        return new self($pagination, $this->sortings, $this->filters);
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }

    /** @param list<Sorting> $sortings */
    public function withSorting(array $sortings): static
    {
        return new self($this->pagination, $sortings, $this->filters);
    }

    /** @return list<Sorting> */
    public function sorting(): array
    {
        return $this->sortings;
    }

    /** @param list<Filter> $filters */
    public function withFilters(array $filters): static
    {
        return new self($this->pagination, $this->sortings, $filters);
    }

    /** @return list<Filter> */
    public function filters(): array
    {
        return $this->filters;
    }
}
