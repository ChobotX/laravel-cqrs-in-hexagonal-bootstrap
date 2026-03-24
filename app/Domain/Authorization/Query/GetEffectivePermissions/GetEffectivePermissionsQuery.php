<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetEffectivePermissions;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\EffectivePermission;

/** @implements Query<list<EffectivePermission>> */
#[RequiresPermission('users.roles.read')]
final readonly class GetEffectivePermissionsQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
