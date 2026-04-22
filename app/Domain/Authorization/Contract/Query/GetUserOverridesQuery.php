<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\ValueObject\UserPermissionOverride;

/**
 * Query for get user overrides in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<list<UserPermissionOverride>>
 */
#[RequiresPermission('users.roles.read')]
final readonly class GetUserOverridesQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
