<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Service;

/**
 * Domain service contract for notification channel sender registry in the Notification bounded context.
 */
interface NotificationChannelSenderRegistry
{
    /** Executes the side effect synchronously. */
    public function senderFor(string $channel): NotificationChannelSender;
}
