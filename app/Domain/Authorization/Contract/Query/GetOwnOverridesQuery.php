<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\ValueObject\UserPermissionOverride;

/**
 * Query for get own overrides in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<list<UserPermissionOverride>>
 */
#[SkipPermissionCheck(reason: 'Users can always view their own permission overrides')]
final readonly class GetOwnOverridesQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
