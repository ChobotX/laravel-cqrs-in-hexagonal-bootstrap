<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\ValueObject\TeamMember;

/**
 * Query for list team members in the Team bounded context; dispatched through the query bus.
 *
 * @implements Query<list<TeamMember>>
 */
#[RequiresPermission('teams.members.read')]
final readonly class ListTeamMembersQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $teamId,
    ) {}
}
