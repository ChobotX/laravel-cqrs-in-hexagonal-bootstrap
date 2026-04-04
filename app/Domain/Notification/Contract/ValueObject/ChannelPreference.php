<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\ValueObject;

use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Enum\NotificationLevel;

final readonly class ChannelPreference
{
    /** @param list<NotificationChannel> $channels */
    public function __construct(
        public NotificationLevel $level,
        public array $channels,
    ) {}
}
