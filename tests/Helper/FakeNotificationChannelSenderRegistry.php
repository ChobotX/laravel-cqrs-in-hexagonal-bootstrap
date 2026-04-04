<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Notification\Contract\Service\NotificationChannelSender;
use App\Domain\Notification\Contract\Service\NotificationChannelSenderRegistry;

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
