<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Query\GetNotificationPreferencesQuery;
use App\Domain\Notification\Contract\ValueObject\ChannelPreference;
use App\Domain\User\Contract\Query\GetOwnProfileQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\View\View;

#[SkipPermissionCheck(reason: 'Profile page is accessible to all authenticated users')]
final readonly class ShowProfileController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(): View
    {
        $userId = $this->authenticatedUser->id() ?? '';

        $user = $this->queryBus->dispatch(new GetOwnProfileQuery($userId));
        $canEditEmail = $this->authorizationChecker->can($userId, 'users.list.update');

        $notificationPreferences = $this->queryBus->dispatch(new GetNotificationPreferencesQuery($userId));
        $notificationPreferencesData = array_map(
            static fn (ChannelPreference $channelPreference): array => [
                'level' => $channelPreference->level->value,
                'in_app' => true,
                'email' => in_array(NotificationChannel::Email, $channelPreference->channels, true),
            ],
            $notificationPreferences->preferences,
        );

        return view('profile.edit', [
            'user' => $user,
            'canEditEmail' => $canEditEmail,
            'notificationPreferences' => $notificationPreferencesData,
        ]);
    }
}
