<?php

declare(strict_types=1);

namespace App\Domain\Notification\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Query\GetNotificationPreferencesQuery;
use App\Domain\Notification\Contract\Repository\NotificationPreferenceRepository;
use App\Domain\Notification\Contract\ValueObject\ChannelPreference;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\ValueObject\NotificationPreferences;

/** @implements QueryHandler<GetNotificationPreferencesQuery, NotificationPreferences> */
final readonly class GetNotificationPreferencesHandler implements QueryHandler
{
    public function __construct(
        private NotificationPreferenceRepository $notificationPreferenceRepository,
    ) {}

    public function handle(Query $query): NotificationPreferences
    {
        $preferences = $this->notificationPreferenceRepository->findByUserId($query->userId);

        if ($preferences instanceof NotificationPreferences) {
            return $preferences;
        }

        return new NotificationPreferences(
            userId: $query->userId,
            preferences: [
                new ChannelPreference(NotificationLevel::Info, [NotificationChannel::InApp]),
                new ChannelPreference(NotificationLevel::Success, [NotificationChannel::InApp]),
                new ChannelPreference(NotificationLevel::Warning, [NotificationChannel::InApp, NotificationChannel::Email]),
                new ChannelPreference(NotificationLevel::Error, [NotificationChannel::InApp, NotificationChannel::Email]),
            ],
        );
    }
}
