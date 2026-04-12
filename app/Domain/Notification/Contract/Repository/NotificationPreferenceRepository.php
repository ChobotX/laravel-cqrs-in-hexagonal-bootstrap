<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Repository;

use App\Domain\Notification\ValueObject\NotificationPreferences;

/**
 * Persistence port for notification preference data in the Notification context; implementations live in Infrastructure.
 */
interface NotificationPreferenceRepository
{
    /** Loads a record or value object, or null when absent. */
    public function findByUserId(string $userId): ?NotificationPreferences;

    /** Persists a new or updated aggregate row. */
    public function save(NotificationPreferences $notificationPreferences): void;
}
