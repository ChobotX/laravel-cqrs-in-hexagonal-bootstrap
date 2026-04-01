<?php

declare(strict_types=1);

namespace App\Domain\Notification;

final readonly class WelcomeNotification
{
    public const string TYPE = 'user.welcome';

    public const string TITLE = 'Welcome!';

    public const string BODY = 'Welcome to the platform. You can manage your notification preferences in your profile settings.';

    public const string LINK = '/profile';
}
