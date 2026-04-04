<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Contract\Query\ListTeamMembersQuery;
use App\Domain\Team\Contract\TeamMember;
use App\Domain\Team\Contract\TeamMemberRepository;

/** @implements QueryHandler<ListTeamMembersQuery, list<TeamMember>> */
final readonly class ListTeamMembersHandler implements QueryHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
    ) {}

    /** @return list<TeamMember> */
    public function handle(Query $query): array
    {
        return $this->teamMemberRepository->listMembers($query->teamId);
    }
}
