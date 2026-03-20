<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\GetUserTeams;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Organization\Team;

/** @implements Query<list<Team>> */
#[SkipPermissionCheck(reason: 'Used for team membership management on user edit form')]
final readonly class GetUserTeamsQuery implements Query
{
    public function __construct(
        public string $userId,
        public string $organizationId,
    ) {}
}
