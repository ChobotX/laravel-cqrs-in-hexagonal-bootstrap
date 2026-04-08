<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipAuditLog;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Dispatched internally by event handlers, not by user action')]
#[SkipAuditLog(reason: 'Internal event-dispatched command, not a user action')]
final readonly class SendNotificationCommand implements Command
{
    /**
     * @param  list<string>  $recipientIds
     */
    public function __construct(
        public array $recipientIds,
        public string $type,
        public string $title,
        public string $body,
        public string $level,
        public ?string $link,
    ) {}
}
