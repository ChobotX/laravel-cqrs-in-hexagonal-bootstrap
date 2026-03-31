<?php

declare(strict_types=1);

namespace App\Domain\Notification;

enum NotificationChannel: string
{
    case InApp = 'in_app';
    case Email = 'email';
}
