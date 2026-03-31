<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\GetTeamTree;

use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Contract\Team\TeamMembershipChecker;
use App\Domain\Team\Team;
use App\Domain\Team\TeamMemberRepository;
use App\Domain\Team\TeamRepository;

/** @implements QueryHandler<GetTeamTreeQuery, list<TeamTreeNode>> */
final readonly class GetTeamTreeHandler implements QueryHandler
{
    private const string SCOPE_ALL = 'all';

    private const string SCOPE_TEAM = 'team';

    public function __construct(
        private TeamRepository $teamRepository,
        private TeamMemberRepository $teamMemberRepository,
        private AuthorizationChecker $authorizationChecker,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    /** @return list<TeamTreeNode> */
    public function handle(Query $query): array
    {
        $teams = $this->resolveAccessibleTeams($query->userId);
        $memberSortings = [new Sorting(Sorting::PERMISSION_SCORE, SortDirection::Desc)];

        return array_map(
            fn (Team $team): TeamTreeNode => new TeamTreeNode(
                team: $team,
                members: $this->teamMemberRepository->listMembers($team->id->value, $memberSortings),
            ),
            $teams,
        );
    }

    /** @return list<Team> */
    private function resolveAccessibleTeams(string $userId): array
    {
        $scope = $this->authorizationChecker->canWithScope($userId, 'teams.management.read')->scope();

        return match ($scope) {
            self::SCOPE_ALL => $this->teamRepository->findAll(),
            self::SCOPE_TEAM => $this->teamRepository->findAll(
                $this->teamMembershipChecker->memberTeamIds($userId),
            ),
            default => [],
        };
    }
}
