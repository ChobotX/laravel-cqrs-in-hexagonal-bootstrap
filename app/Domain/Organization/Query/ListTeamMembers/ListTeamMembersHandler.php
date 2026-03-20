<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\ListTeamMembers;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Organization\TeamMember;
use App\Domain\Organization\TeamMemberRepository;

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
