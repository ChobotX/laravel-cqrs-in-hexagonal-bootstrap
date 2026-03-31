<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Notification;

enum NotificationFilter: string
{
    case Unread = 'unread';
    case Read = 'read';
}
