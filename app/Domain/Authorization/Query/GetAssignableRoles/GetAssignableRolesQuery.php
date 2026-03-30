<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetAssignableRoles;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\Role;

/** @implements Query<list<Role>> */
#[RequiresPermission('users.roles.read')]
final readonly class GetAssignableRolesQuery implements Query
{
    public function __construct(
        public string $assignerUserId,
    ) {}
}
