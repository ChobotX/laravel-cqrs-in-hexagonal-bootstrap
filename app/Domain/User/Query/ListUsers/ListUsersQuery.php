<?php

declare(strict_types=1);

namespace App\Domain\User\Query\ListUsers;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Pagination\PaginableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortableQuery;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Domain\User\User;

/**
 * @implements Query<PaginatedResult<User>>
 */
#[RequiresPermission('users.list.read')]
final readonly class ListUsersQuery implements PaginableQuery, Query, ScopeAwareQuery, SortableQuery
{
    /**
     * @param  list<Sorting>  $sortings
     */
    public function __construct(
        private ?Pagination $pagination = null,
        private ?AccessContext $accessContext = null,
        private array $sortings = [],
    ) {}

    public function withPagination(Pagination $pagination): static
    {
        return new self($pagination, $this->accessContext, $this->sortings);
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }

    public function withAccessContext(AccessContext $accessContext): static
    {
        return new self($this->pagination, $accessContext, $this->sortings);
    }

    public function accessContext(): ?AccessContext
    {
        return $this->accessContext;
    }

    /** @param list<Sorting> $sortings */
    public function withSorting(array $sortings): static
    {
        return new self($this->pagination, $this->accessContext, $sortings);
    }

    /** @return list<Sorting> */
    public function sorting(): array
    {
        return $this->sortings;
    }
}
