<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * Returns the user IDs of every member in the given team and all descendant subteams.
 * Used for cross-module notification fan-out and similar internal orchestration.
 *
 * @implements Query<list<string>>
 */
#[SkipPermissionCheck(reason: 'Used internally for notification fan-out; not a user-facing read')]
final readonly class ListTeamSubtreeMemberUserIdsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $teamId,
    ) {}
}
