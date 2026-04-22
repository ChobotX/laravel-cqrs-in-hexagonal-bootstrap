<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\Entity\Role;

/**
 * Query for get user roles in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<list<Role>>
 */
#[RequiresPermission('users.roles.read')]
final readonly class GetUserRolesQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
