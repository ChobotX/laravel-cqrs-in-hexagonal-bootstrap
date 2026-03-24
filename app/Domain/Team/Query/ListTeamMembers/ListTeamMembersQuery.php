<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\ListTeamMembers;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Team\TeamMember;

/** @implements Query<list<TeamMember>> */
#[RequiresPermission('teams.members.read')]
final readonly class ListTeamMembersQuery implements Query
{
    public function __construct(
        public string $teamId,
    ) {}
}
