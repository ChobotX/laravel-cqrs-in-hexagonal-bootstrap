<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\ValueObject;

use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Enum\NotificationLevel;

/**
 * Contract-level value object for channel preference used across Notification commands, queries, and events.
 */
final readonly class ChannelPreference
{
    /** @param list<NotificationChannel> $channels */
    public function __construct(
        /** Field `level` for this contract; see module docs for validation rules. */
        public NotificationLevel $level,
        /** Array for `channels`; see constructor PHPDoc for structural tags when present. */
        public array $channels,
    ) {}
}
