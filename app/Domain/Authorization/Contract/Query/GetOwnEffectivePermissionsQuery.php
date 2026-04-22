<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\ValueObject\EffectivePermission;

/**
 * Query for get own effective permissions in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<list<EffectivePermission>>
 */
#[SkipPermissionCheck(reason: 'Users can always view their own effective permissions')]
final readonly class GetOwnEffectivePermissionsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
