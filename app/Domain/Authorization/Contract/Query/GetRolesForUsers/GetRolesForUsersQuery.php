<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query\GetRolesForUsers;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\Role;

/** @implements Query<array<string, list<Role>>> */
#[RequiresPermission('users.roles.read')]
final readonly class GetRolesForUsersQuery implements Query
{
    /** @param list<string> $userIds */
    public function __construct(
        public array $userIds,
    ) {}
}
