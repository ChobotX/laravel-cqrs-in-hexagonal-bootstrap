<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;

/** @implements Query<int> */
#[SkipPermissionCheck(reason: 'Users count only their own unread notifications')]
final readonly class CountUnreadNotificationsQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
