<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\User\Contract\Entity\User;

/**
 * Query for get own profile in the User bounded context; dispatched through the query bus.
 *
 * @implements Query<User>
 */
#[SkipPermissionCheck(reason: 'Users can always view their own profile')]
final readonly class GetOwnProfileQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
