<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Entity\Team;

/** @implements Query<list<Team>> */
#[SkipPermissionCheck(reason: 'Used for team membership management on user edit form')]
final readonly class GetUserTeamsQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
