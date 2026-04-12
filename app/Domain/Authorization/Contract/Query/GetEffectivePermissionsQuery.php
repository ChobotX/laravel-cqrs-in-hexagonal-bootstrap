<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\ValueObject\EffectivePermission;

/**
 * Query for get effective permissions in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<list<EffectivePermission>>
 */
#[RequiresPermission('users.roles.read')]
final readonly class GetEffectivePermissionsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
