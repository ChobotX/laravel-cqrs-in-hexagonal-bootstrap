<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Contract\Notification\NotificationChannelSender;
use App\Contract\Notification\NotificationChannelSenderRegistry;
use App\Infrastructure\Notification\Exception\UnsupportedNotificationChannelException;

final readonly class ChannelSenderRegistry implements NotificationChannelSenderRegistry
{
    /** @param array<string, NotificationChannelSender> $senders */
    public function __construct(
        private array $senders,
    ) {}

    public function senderFor(string $channel): NotificationChannelSender
    {
        if (! isset($this->senders[$channel])) {
            throw new UnsupportedNotificationChannelException($channel);
        }

        return $this->senders[$channel];
    }
}
