<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * Query for count unread notifications in the Notification bounded context; dispatched through the query bus.
 *
 * @implements Query<int>
 */
#[SkipPermissionCheck(reason: 'Users count only their own unread notifications')]
final readonly class CountUnreadNotificationsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
