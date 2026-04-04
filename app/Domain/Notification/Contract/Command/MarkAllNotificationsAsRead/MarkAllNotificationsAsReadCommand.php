<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command\MarkAllNotificationsAsRead;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users mark only their own notifications as read')]
final readonly class MarkAllNotificationsAsReadCommand implements Command
{
    public function __construct(
        public string $userId,
    ) {}
}
