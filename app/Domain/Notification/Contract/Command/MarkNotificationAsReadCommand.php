<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Ownership enforced in handler')]
final readonly class MarkNotificationAsReadCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $notificationId,
        public string $userId,
    ) {}

    public function auditEntityType(): string
    {
        return 'notification';
    }

    public function auditEntityId(): string
    {
        return $this->notificationId;
    }
}
