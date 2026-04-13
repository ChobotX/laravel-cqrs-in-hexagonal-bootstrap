<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Service;

/**
 * Domain service contract for notification channel sender in the Notification bounded context.
 */
interface NotificationChannelSender
{
    /** Executes the side effect synchronously. */
    public function send(
        string $recipientId,
        string $recipientEmail,
        string $type,
        string $title,
        string $body,
        string $level,
        ?string $link,
    ): void;

    /** Contract operation `supports`; see infrastructure for behavior. */
    public function supports(): string;
}
