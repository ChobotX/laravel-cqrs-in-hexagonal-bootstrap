<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\GetTeamTree;

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

        return array_map(
            fn (Team $team): TeamTreeNode => new TeamTreeNode(
                team: $team,
                members: $this->teamMemberRepository->listMembers($team->id->value),
            ),
            $teams,
        );
    }

    /** @return list<Team> */
    private function resolveAccessibleTeams(string $userId): array
    {
        $scope = $this->authorizationChecker->canWithScope($userId, 'teams.management.read')->scope();

        return match ($scope) {
            'all' => $this->teamRepository->findAll(),
            'team' => $this->teamRepository->findAll(
                $this->teamMembershipChecker->memberTeamIds($userId),
            ),
            default => [],
        };
    }
}
