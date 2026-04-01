<?php

declare(strict_types=1);

namespace App\Domain\Notification\Query\GetNotificationPreferences;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Notification\ChannelPreference;
use App\Domain\Notification\Contract\NotificationPreferenceRepository;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationPreferences;

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
