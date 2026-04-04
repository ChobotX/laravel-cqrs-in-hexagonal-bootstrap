<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Entity\Team;

/** @implements Query<array<string, list<Team>>> */
#[SkipPermissionCheck(reason: 'Used for team membership display on user list, gated by parent list permission')]
final readonly class GetTeamsForUsersQuery implements Query
{
    /** @param list<string> $userIds */
    public function __construct(
        public array $userIds,
    ) {}
}
