<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Notification\NotificationPreferenceRepository;
use App\Domain\Notification\NotificationPreferences;

final class FakeNotificationPreferenceRepository implements NotificationPreferenceRepository
{
    /** @var list<NotificationPreferences> */
    public array $saved = [];

    /** @param array<string, NotificationPreferences> $preferences */
    public function __construct(
        private array $preferences = [],
    ) {}

    public function findByUserId(string $userId): ?NotificationPreferences
    {
        return $this->preferences[$userId] ?? null;
    }

    public function save(NotificationPreferences $notificationPreferences): void
    {
        $this->saved[] = $notificationPreferences;
        $this->preferences[$notificationPreferences->userId] = $notificationPreferences;
    }
}
