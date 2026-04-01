<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract;

use App\Domain\Notification\NotificationPreferences;

interface NotificationPreferenceRepository
{
    public function findByUserId(string $userId): ?NotificationPreferences;

    public function save(NotificationPreferences $notificationPreferences): void;
}
