<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\ListTeams;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Contract\Team\TeamMembershipChecker;
use App\Domain\Team\Team;
use App\Domain\Team\TeamRepository;

/** @implements QueryHandler<ListTeamsQuery, PaginatedResult<Team>> */
final readonly class ListTeamsHandler implements QueryHandler
{
    private const string SCOPE_ALL = 'all';

    private const string SCOPE_TEAM = 'team';

    public function __construct(
        private TeamRepository $teamRepository,
        private AuthorizationChecker $authorizationChecker,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    /** @return PaginatedResult<Team> */
    public function handle(Query $query): PaginatedResult
    {
        $pagination = $query->pagination();
        $sortings = $query->sorting() !== [] ? $query->sorting() : [new Sorting('name', SortDirection::Asc)];
        $scope = $this->authorizationChecker->canWithScope($query->userId, 'teams.management.read')->scope();

        if ($pagination !== null) {
            return match ($scope) {
                self::SCOPE_ALL => $this->teamRepository->findAllPaginated($pagination, null, $sortings),
                self::SCOPE_TEAM => $this->teamRepository->findAllPaginated(
                    $pagination,
                    $this->teamMembershipChecker->memberTeamIds($query->userId),
                    $sortings,
                ),
                default => new PaginatedResult([], 0, $pagination),
            };
        }

        $items = match ($scope) {
            self::SCOPE_ALL => $this->teamRepository->findAll(null, $sortings),
            self::SCOPE_TEAM => $this->teamRepository->findAll(
                $this->teamMembershipChecker->memberTeamIds($query->userId),
                $sortings,
            ),
            default => [],
        };

        return new PaginatedResult($items, count($items), new Pagination(1, max(count($items), 1)));
    }
}
