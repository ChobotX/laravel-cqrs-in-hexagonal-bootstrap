<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Notification;

use App\Application\Authorization\SkipPermissionCheck;
use Illuminate\View\View;

#[SkipPermissionCheck(reason: 'Notifications page is accessible to all authenticated users')]
final readonly class ShowNotificationsController
{
    public function __invoke(): View
    {
        return view('notifications.index');
    }
}
