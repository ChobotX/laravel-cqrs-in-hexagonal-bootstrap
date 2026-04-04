<?php

declare(strict_types=1);

namespace App\Domain\Notification\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Notification\Contract\Command\UpdateNotificationPreferencesCommand;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Event\NotificationPreferencesUpdated;
use App\Domain\Notification\Contract\Repository\NotificationPreferenceRepository;
use App\Domain\Notification\Contract\ValueObject\ChannelPreference;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\ValueObject\NotificationPreferences;
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
