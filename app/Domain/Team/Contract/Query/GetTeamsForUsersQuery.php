<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Entity\Team;

/**
 * Query for get teams for users in the Team bounded context; dispatched through the query bus.
 *
 * @implements Query<array<string, list<Team>>>
 */
#[SkipPermissionCheck(reason: 'Used for team membership display on user list, gated by parent list permission')]
final readonly class GetTeamsForUsersQuery implements Query
{
    /** @param list<string> $userIds */
    public function __construct(
        /** List of stable identifiers for batch operations. */
        public array $userIds,
    ) {}
}
