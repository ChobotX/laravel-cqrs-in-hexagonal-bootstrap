<?php

declare(strict_types=1);

namespace App\Domain\Notification;

use App\Domain\Notification\Contract\ChannelPreference;

final readonly class NotificationPreferences
{
    /** @param list<ChannelPreference> $preferences */
    public function __construct(
        public string $userId,
        public array $preferences,
    ) {}
}
