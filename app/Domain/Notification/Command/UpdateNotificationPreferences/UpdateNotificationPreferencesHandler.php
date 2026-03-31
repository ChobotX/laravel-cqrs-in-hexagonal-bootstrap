<?php

declare(strict_types=1);

namespace App\Domain\Notification\Command\UpdateNotificationPreferences;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Notification\ChannelPreference;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationPreferenceRepository;
use App\Domain\Notification\NotificationPreferences;

/** @implements CommandHandler<UpdateNotificationPreferencesCommand> */
final readonly class UpdateNotificationPreferencesHandler implements CommandHandler
{
    public function __construct(
        private NotificationPreferenceRepository $notificationPreferenceRepository,
    ) {}

    public function handle(Command $command): void
    {
        $channelPreferences = [];

        foreach ($command->preferences as $levelValue => $channelValues) {
            $level = NotificationLevel::from($levelValue);

            $channels = array_map(
                NotificationChannel::from(...),
                $channelValues,
            );

            if (! in_array(NotificationChannel::InApp, $channels, true)) {
                $channels[] = NotificationChannel::InApp;
            }

            $channelPreferences[] = new ChannelPreference($level, $channels);
        }

        $this->notificationPreferenceRepository->save(new NotificationPreferences(
            userId: $command->userId,
            preferences: $channelPreferences,
        ));
    }
}
