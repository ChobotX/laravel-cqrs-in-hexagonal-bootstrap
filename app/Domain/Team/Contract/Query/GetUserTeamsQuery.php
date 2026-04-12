<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Entity\Team;

/**
 * Query for get user teams in the Team bounded context; dispatched through the query bus.
 *
 * @implements Query<list<Team>>
 */
#[SkipPermissionCheck(reason: 'Used for team membership management on user edit form')]
final readonly class GetUserTeamsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
