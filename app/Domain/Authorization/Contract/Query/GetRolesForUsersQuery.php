<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\Entity\Role;

/**
 * Query for get roles for users in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<array<string, list<Role>>>
 */
#[RequiresPermission('users.roles.read')]
final readonly class GetRolesForUsersQuery implements Query
{
    /** @param list<string> $userIds */
    public function __construct(
        /** List of stable identifiers for batch operations. */
        public array $userIds,
    ) {}
}
