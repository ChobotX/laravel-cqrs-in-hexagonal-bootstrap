<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Authorization\ScopeTarget;
use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterableQuery;
use App\Application\Pagination\PaginableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortableQuery;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Entity\Team;

/**
 * Query for list teams in the Team bounded context; dispatched through the query bus.
 *
 * @implements Query<PaginatedResult<Team>>
 */
#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsQuery implements FilterableQuery, PaginableQuery, Query, ScopeAwareQuery, SortableQuery
{
    /**
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     */
    public function __construct(
        private ?Pagination $pagination = null,
        private ?AccessContext $accessContext = null,
        private array $sortings = [],
        private array $filters = [],
    ) {}

    public function withPagination(Pagination $pagination): static
    {
        return new self($pagination, $this->accessContext, $this->sortings, $this->filters);
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }

    public function scopeTarget(): ScopeTarget
    {
        return ScopeTarget::Team;
    }

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($this->pagination, $accessContext, $this->sortings, $this->filters);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }

    /** @param list<Sorting> $sortings */
    public function withSorting(array $sortings): static
    {
        return new self($this->pagination, $this->accessContext, $sortings, $this->filters);
    }

    /** @return list<Sorting> */
    public function sorting(): array
    {
        return $this->sortings;
    }

    /** @param list<Filter> $filters */
    public function withFilters(array $filters): static
    {
        return new self($this->pagination, $this->accessContext, $this->sortings, $filters);
    }

    /** @return list<Filter> */
    public function filters(): array
    {
        return $this->filters;
    }
}
