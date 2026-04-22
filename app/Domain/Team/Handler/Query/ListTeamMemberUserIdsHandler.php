<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Contract\Query\ListTeamMemberUserIdsQuery;
use App\Domain\Team\Contract\Repository\TeamMemberRepository;

/** @implements QueryHandler<ListTeamMemberUserIdsQuery, list<string>> */
final readonly class ListTeamMemberUserIdsHandler implements QueryHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
    ) {}

    /** @return list<string> */
    public function handle(Query $query): array
    {
        return $this->teamMemberRepository->memberUserIdsForTeam($query->teamId);
    }
}
