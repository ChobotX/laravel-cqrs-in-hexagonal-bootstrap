<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Query\GetNotificationPreferences;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Notification\NotificationPreferences;

/** @implements Query<NotificationPreferences> */
#[SkipPermissionCheck(reason: 'Users view only their own notification preferences')]
final readonly class GetNotificationPreferencesQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
