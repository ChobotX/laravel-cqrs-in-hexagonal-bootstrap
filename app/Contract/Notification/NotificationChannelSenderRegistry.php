<?php

declare(strict_types=1);

namespace App\Contract\Notification;

interface NotificationChannelSenderRegistry
{
    public function senderFor(string $channel): NotificationChannelSender;
}
