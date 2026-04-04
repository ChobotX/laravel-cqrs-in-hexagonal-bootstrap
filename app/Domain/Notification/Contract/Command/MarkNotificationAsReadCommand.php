<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Ownership enforced in handler')]
final readonly class MarkNotificationAsReadCommand implements Command
{
    public function __construct(
        public string $notificationId,
        public string $userId,
    ) {}
}
