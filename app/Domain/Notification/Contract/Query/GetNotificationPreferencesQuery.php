<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Notification\ValueObject\NotificationPreferences;

/**
 * Query for get notification preferences in the Notification bounded context; dispatched through the query bus.
 *
 * @implements Query<NotificationPreferences>
 */
#[SkipPermissionCheck(reason: 'Users view only their own notification preferences')]
final readonly class GetNotificationPreferencesQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
