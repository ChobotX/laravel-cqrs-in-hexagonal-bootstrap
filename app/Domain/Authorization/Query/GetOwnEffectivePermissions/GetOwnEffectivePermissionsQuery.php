<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetOwnEffectivePermissions;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\EffectivePermission;

/** @implements Query<list<EffectivePermission>> */
#[SkipPermissionCheck(reason: 'Users can always view their own effective permissions')]
final readonly class GetOwnEffectivePermissionsQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
