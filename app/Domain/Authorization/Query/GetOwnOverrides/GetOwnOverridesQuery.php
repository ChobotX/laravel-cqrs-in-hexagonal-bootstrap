<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetOwnOverrides;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Authorization\UserPermissionOverride;

/** @implements Query<list<UserPermissionOverride>> */
#[SkipPermissionCheck(reason: 'Users can always view their own permission overrides')]
final readonly class GetOwnOverridesQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
