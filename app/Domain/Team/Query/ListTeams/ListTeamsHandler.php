<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\ListTeams;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Team;
use App\Domain\Team\TeamRepository;

/** @implements QueryHandler<ListTeamsQuery, PaginatedResult<Team>> */
final readonly class ListTeamsHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
    ) {}

    /** @return PaginatedResult<Team> */
    public function handle(Query $query): PaginatedResult
    {
        $pagination = $query->pagination();
        $sortings = $query->sorting() !== [] ? $query->sorting() : [new Sorting('name', SortDirection::Asc)];
        $visibleIds = $query->accessContext()?->visibleIds;

        if ($pagination !== null) {
            return $this->teamRepository->findAllPaginated($pagination, $visibleIds, $sortings);
        }

        $items = $this->teamRepository->findAll($visibleIds, $sortings);

        return new PaginatedResult($items, count($items), new Pagination(1, max(count($items), 1)));
    }
}
