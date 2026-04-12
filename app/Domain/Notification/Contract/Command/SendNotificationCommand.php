<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for send notification in the Notification bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Dispatched internally by event handlers, not by user action')]
final readonly class SendNotificationCommand implements Command
{
    /**
     * @param  list<string>  $recipientIds
     */
    public function __construct(
        /** List of stable identifiers for batch operations. */
        public array $recipientIds,
        /** Field `type` for this contract; see module docs for validation rules. */
        public string $type,
        /** Human-visible label or title. */
        public string $title,
        /** Field `body` for this contract; see module docs for validation rules. */
        public string $body,
        /** Field `level` for this contract; see module docs for validation rules. */
        public string $level,
        /** Optional `link`; null means not provided or not applicable. */
        public ?string $link,
    ) {}
}
