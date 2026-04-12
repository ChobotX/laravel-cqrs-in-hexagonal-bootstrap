<?php

declare(strict_types=1);

namespace App\Domain\Notification\Handler\Command;

use App\Application\Event\PropertyChange;
use App\Application\Event\PropertyChangeBuilder;
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
        private PropertyChangeBuilder $propertyChangeBuilder,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->notificationPreferenceRepository->findByUserId($command->userId);

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

        $notificationPreferences = new NotificationPreferences(
            userId: $command->userId,
            preferences: $channelPreferences,
        );

        $changes = $this->buildChanges($existing, $notificationPreferences);

        if ($changes === []) {
            return;
        }

        $this->notificationPreferenceRepository->save($notificationPreferences);

        $this->eventCollector->collect(new NotificationPreferencesUpdated(
            userId: $command->userId,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        ));
    }

    /** @return list<PropertyChange> */
    private function buildChanges(?NotificationPreferences $existing, NotificationPreferences $updated): array
    {
        $oldSerialized = $this->serializePreferences($existing);
        $newSerialized = $this->serializePreferences($updated);

        $allLevels = array_unique(array_merge(array_keys($oldSerialized), array_keys($newSerialized)));
        sort($allLevels);

        $pairs = [];
        foreach ($allLevels as $allLevel) {
            $pairs[$allLevel] = [$oldSerialized[$allLevel] ?? null, $newSerialized[$allLevel] ?? null];
        }

        return $this->propertyChangeBuilder->diff($pairs);
    }

    /** @return array<string, string> */
    private function serializePreferences(?NotificationPreferences $notificationPreferences): array
    {
        if (! $notificationPreferences instanceof NotificationPreferences) {
            return [];
        }

        $result = [];

        foreach ($notificationPreferences->preferences as $channelPreference) {
            $channels = array_map(
                static fn (NotificationChannel $notificationChannel): string => $notificationChannel->value,
                $channelPreference->channels,
            );
            sort($channels);
            $result[$channelPreference->level->value] = implode(',', $channels);
        }

        return $result;
    }
}
