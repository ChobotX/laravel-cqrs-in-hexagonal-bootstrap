<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users mark only their own notifications as read')]
final readonly class MarkAllNotificationsAsReadCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $userId,
    ) {}

    public function auditEntityType(): string
    {
        return 'notification';
    }

    public function auditEntityId(): string
    {
        return $this->userId;
    }
}
