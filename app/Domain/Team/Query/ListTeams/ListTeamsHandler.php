<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\ListTeams;

use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Contract\Team\TeamMembershipChecker;
use App\Domain\Team\Team;
use App\Domain\Team\TeamRepository;

/** @implements QueryHandler<ListTeamsQuery, list<Team>> */
final readonly class ListTeamsHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
        private AuthorizationChecker $authorizationChecker,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    /** @return list<Team> */
    public function handle(Query $query): array
    {
        $scope = $this->authorizationChecker->canWithScope($query->userId, 'teams.management.read')->scope();

        return match ($scope) {
            'all' => $this->teamRepository->findAll(),
            'team' => $this->teamRepository->findAll(
                $this->teamMembershipChecker->memberTeamIds($query->userId),
            ),
            default => [],
        };
    }
}
