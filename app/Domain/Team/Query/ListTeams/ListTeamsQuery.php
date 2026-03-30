<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\ListTeams;

use App\Application\Authorization\RequiresPermission;
use App\Application\Pagination\PaginableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortableQuery;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Domain\Team\Team;

/**
 * @implements Query<PaginatedResult<Team>>
 */
#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsQuery implements PaginableQuery, Query, SortableQuery
{
    /**
     * @param  list<Sorting>  $sortings
     */
    public function __construct(
        public string $userId,
        private ?Pagination $pagination = null,
        private array $sortings = [],
    ) {}

    public function withPagination(Pagination $pagination): static
    {
        return new self($this->userId, $pagination, $this->sortings);
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }

    /** @param list<Sorting> $sortings */
    public function withSorting(array $sortings): static
    {
        return new self($this->userId, $this->pagination, $sortings);
    }

    /** @return list<Sorting> */
    public function sorting(): array
    {
        return $this->sortings;
    }
}
