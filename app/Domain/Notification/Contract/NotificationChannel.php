<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract;

enum NotificationChannel: string
{
    case InApp = 'in_app';
    case Email = 'email';
}
