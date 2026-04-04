<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\GetUserTeams;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamMemberRepository;
use App\Domain\Team\Contract\TeamRepository;

/** @implements QueryHandler<GetUserTeamsQuery, list<Team>> */
final readonly class GetUserTeamsHandler implements QueryHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
        private TeamRepository $teamRepository,
    ) {}

    /** @return list<Team> */
    public function handle(Query $query): array
    {
        $teamIds = $this->teamMemberRepository->directMemberTeamIds($query->userId);

        if ($teamIds === []) {
            return [];
        }

        return $this->teamRepository->findAll($teamIds);
    }
}
