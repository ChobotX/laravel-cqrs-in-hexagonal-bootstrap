<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Service;

interface NotificationChannelSenderRegistry
{
    public function senderFor(string $channel): NotificationChannelSender;
}
