<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for delete notification in the Notification bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Ownership enforced in handler')]
final readonly class DeleteNotificationCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $notificationId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
    ) {}
}
