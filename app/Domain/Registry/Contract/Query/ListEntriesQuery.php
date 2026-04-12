<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Authorization\ScopeTarget;
use App\Application\Authorization\ShareableScopeQuery;
use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Application\Sorting\SortableQuery;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Domain\Registry\Contract\Entity\Entry;

/**
 * Query for list entries in the Registry bounded context; dispatched through the query bus.
 *
 * @implements Query<PaginatedResult<Entry>>
 */
#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesQuery implements FilterableQuery, Query, ScopeAwareQuery, ShareableScopeQuery, SortableQuery
{
    public const int DEFAULT_PER_PAGE = 15;

    /**
     * @param  list<Filter>  $filters
     * @param  list<Sorting>  $sortings
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $definitionId,
        /** Field `page` for this contract; see module docs for validation rules. */
        public int $page = 1,
        /** Field `perPage` for this contract; see module docs for validation rules. */
        public int $perPage = self::DEFAULT_PER_PAGE,
        private array $filters = [],
        private array $sortings = [],
        private ?AccessContext $accessContext = null,
    ) {}

    public function scopeTarget(): ScopeTarget
    {
        return ScopeTarget::User;
    }

    public function shareableResourceType(): string
    {
        return 'entry';
    }

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($this->definitionId, $this->page, $this->perPage, $this->filters, $this->sortings, $accessContext);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }

    /** @param list<Filter> $filters */
    public function withFilters(array $filters): static
    {
        return new self($this->definitionId, $this->page, $this->perPage, $filters, $this->sortings, $this->accessContext);
    }

    /** @return list<Filter> */
    public function filters(): array
    {
        return $this->filters;
    }

    /** @param list<Sorting> $sortings */
    public function withSorting(array $sortings): static
    {
        return new self($this->definitionId, $this->page, $this->perPage, $this->filters, $sortings, $this->accessContext);
    }

    /** @return list<Sorting> */
    public function sorting(): array
    {
        return $this->sortings;
    }
}
