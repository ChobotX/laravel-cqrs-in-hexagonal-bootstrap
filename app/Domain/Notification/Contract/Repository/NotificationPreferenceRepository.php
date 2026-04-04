<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Repository;

use App\Domain\Notification\ValueObject\NotificationPreferences;

interface NotificationPreferenceRepository
{
    public function findByUserId(string $userId): ?NotificationPreferences;

    public function save(NotificationPreferences $notificationPreferences): void;
}
