<?php

declare(strict_types=1);

namespace App\Contract\Notification;

interface NotificationChannelSender
{
    public function send(
        string $recipientId,
        string $type,
        string $title,
        string $body,
        string $level,
        ?string $link,
    ): void;

    public function supports(): string;
}
