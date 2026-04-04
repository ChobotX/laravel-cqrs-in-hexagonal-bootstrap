<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\GetTeamsForUsers;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Contract\Query\GetTeamsForUsers\GetTeamsForUsersQuery;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamMemberRepository;
use App\Domain\Team\Contract\TeamRepository;

/** @implements QueryHandler<GetTeamsForUsersQuery, array<string, list<Team>>> */
final readonly class GetTeamsForUsersHandler implements QueryHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
        private TeamRepository $teamRepository,
    ) {}

    /** @return array<string, list<Team>> */
    public function handle(Query $query): array
    {
        $teamIdsByUser = $this->teamMemberRepository->directMemberTeamIdsForUsers($query->userIds);

        $allTeamIds = array_values(array_unique(array_merge(...array_values($teamIdsByUser))));

        if ($allTeamIds === []) {
            return array_fill_keys($query->userIds, []);
        }

        $teams = $this->teamRepository->findAll($allTeamIds);
        $teamsById = [];

        foreach ($teams as $team) {
            $teamsById[$team->id->value] = $team;
        }

        /** @var array<string, list<Team>> $map */
        $map = [];

        foreach ($teamIdsByUser as $userId => $teamIds) {
            $map[$userId] = array_values(array_filter(
                array_map(fn (string $id): ?Team => $teamsById[$id] ?? null, $teamIds),
                fn (?Team $team): bool => $team instanceof Team,
            ));
        }

        return $map;
    }
}
