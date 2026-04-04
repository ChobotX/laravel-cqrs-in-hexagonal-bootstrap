<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command\SendNotification;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Dispatched internally by event handlers, not by user action')]
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
