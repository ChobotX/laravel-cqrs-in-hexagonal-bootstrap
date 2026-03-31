<?php

declare(strict_types=1);

namespace App\Domain\Notification;

final readonly class ChannelPreference
{
    /** @param list<NotificationChannel> $channels */
    public function __construct(
        public NotificationLevel $level,
        public array $channels,
    ) {}
}
