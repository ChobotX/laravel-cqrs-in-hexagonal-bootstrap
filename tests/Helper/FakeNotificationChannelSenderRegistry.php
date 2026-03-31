<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Notification\NotificationChannelSender;
use App\Contract\Notification\NotificationChannelSenderRegistry;

final readonly class FakeNotificationChannelSenderRegistry implements NotificationChannelSenderRegistry
{
    public function __construct(
        private NotificationChannelSender $notificationChannelSender,
    ) {}

    public function senderFor(string $channel): NotificationChannelSender
    {
        return $this->notificationChannelSender;
    }
}
