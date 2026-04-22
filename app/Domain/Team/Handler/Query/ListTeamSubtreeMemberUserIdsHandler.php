<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Contract\Query\ListTeamSubtreeMemberUserIdsQuery;
use App\Domain\Team\Contract\Repository\TeamMemberRepository;

/** @implements QueryHandler<ListTeamSubtreeMemberUserIdsQuery, list<string>> */
final readonly class ListTeamSubtreeMemberUserIdsHandler implements QueryHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
    ) {}

    /** @return list<string> */
    public function handle(Query $query): array
    {
        return $this->teamMemberRepository->memberUserIdsForTeamSubtree($query->teamId);
    }
}
