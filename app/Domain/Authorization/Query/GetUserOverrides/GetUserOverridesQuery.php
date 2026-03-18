<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetUserOverrides;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\UserPermissionOverride;

/** @implements Query<list<UserPermissionOverride>> */
#[RequiresPermission('users.roles.read')]
final readonly class GetUserOverridesQuery implements Query
{
    public function __construct(
        public string $userId,
        public string $organizationId,
    ) {}
}
