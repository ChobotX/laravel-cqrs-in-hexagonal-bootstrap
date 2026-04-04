<?php

declare(strict_types=1);

namespace App\Domain\Notification\Enum;

enum NotificationLevel: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';
}
