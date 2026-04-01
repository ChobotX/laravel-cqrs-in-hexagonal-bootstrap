<?php

declare(strict_types=1);

namespace App\Domain\Notification\Command\UpdateNotificationPreferences;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Notification\ChannelPreference;
use App\Domain\Notification\Contract\Event\NotificationPreferencesUpdated;
use App\Domain\Notification\Contract\NotificationPreferenceRepository;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationPreferences;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateNotificationPreferencesCommand> */
final readonly class UpdateNotificationPreferencesHandler implements CommandHandler
{
    public function __construct(
        private NotificationPreferenceRepository $notificationPreferenceRepository,
        private EventCollector $eventCollector,
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

        $this->eventCollector->collect(new NotificationPreferencesUpdated(
            userId: $command->userId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
