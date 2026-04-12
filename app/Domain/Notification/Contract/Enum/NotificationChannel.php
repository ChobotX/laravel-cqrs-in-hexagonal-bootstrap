<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Enum;

/**
 * Enumerates allowed values for notification channel in the Notification context.
 */
enum NotificationChannel: string
{
    case InApp = 'in_app';
    case Email = 'email';
}
