<?php

declare(strict_types=1);

namespace App\Domain\Notification\Handler\Command;

use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\IdGenerator;
use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\Notification\Contract\Command\SendNotificationCommand;
use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Event\NotificationCreated;
use App\Domain\Notification\Contract\Repository\NotificationPreferenceRepository;
use App\Domain\Notification\Contract\Repository\NotificationRepository;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\ValueObject\NotificationLink;
use App\Domain\Notification\ValueObject\NotificationType;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use DateTimeImmutable;

/** @implements CommandHandler<SendNotificationCommand> */
final readonly class SendNotificationHandler implements CommandHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private NotificationPreferenceRepository $notificationPreferenceRepository,
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private Translator $translator,
        private IdGenerator $idGenerator,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $notificationType = new NotificationType($command->type);
        $notificationLevel = NotificationLevel::from($command->level);
        $link = $command->link !== null ? new NotificationLink($command->link) : null;

        foreach ($command->recipientIds as $recipientId) {
            $this->sendToRecipient($recipientId, $notificationType, $command->title, $command->body, $notificationLevel, $link);
        }
    }

    private function sendToRecipient(
        string $recipientId,
        NotificationType $notificationType,
        string $title,
        string $body,
        NotificationLevel $notificationLevel,
        ?NotificationLink $notificationLink,
    ): void {
        try {
            $this->queryBus->dispatch(new GetUserByIdQuery(id: $recipientId));
        } catch (UserNotFoundException) {
            // @silent: unknown recipient means stale input; silently skip rather than fail entire batch.
            return;
        }

        $channels = $this->resolveChannels($recipientId, $notificationLevel);

        foreach ($channels as $channel) {
            match ($channel) {
                NotificationChannel::InApp => $this->createInAppNotification($recipientId, $notificationType, $title, $body, $notificationLevel, $notificationLink, $channel),
                NotificationChannel::Email => $this->sendEmailNotification($recipientId, $title, $body, $notificationLink),
            };
        }
    }

    private function sendEmailNotification(
        string $recipientId,
        string $title,
        string $body,
        ?NotificationLink $notificationLink,
    ): void {
        $this->commandBus->dispatch(new SendTemplatedEmailCommand(
            userId: $recipientId,
            templateType: 'notification',
            locale: $this->translator->locale(),
            variables: [
                'title' => $title,
                'body' => $body,
                'link' => $notificationLink?->value,
            ],
        ));
    }

    /** @return list<NotificationChannel> */
    private function resolveChannels(string $recipientId, NotificationLevel $notificationLevel): array
    {
        $preferences = $this->notificationPreferenceRepository->findByUserId($recipientId);

        if ($preferences instanceof \App\Domain\Notification\ValueObject\NotificationPreferences) {
            foreach ($preferences->preferences as $pref) {
                if ($pref->level === $notificationLevel) {
                    return $pref->channels;
                }
            }
        }

        return $this->defaultChannelsForLevel($notificationLevel);
    }

    /** @return list<NotificationChannel> */
    private function defaultChannelsForLevel(NotificationLevel $notificationLevel): array
    {
        return match ($notificationLevel) {
            NotificationLevel::Info, NotificationLevel::Success => [NotificationChannel::InApp],
            NotificationLevel::Warning, NotificationLevel::Error => [NotificationChannel::InApp, NotificationChannel::Email],
        };
    }

    private function createInAppNotification(
        string $recipientId,
        NotificationType $notificationType,
        string $title,
        string $body,
        NotificationLevel $notificationLevel,
        ?NotificationLink $notificationLink,
        NotificationChannel $notificationChannel,
    ): void {
        $notificationId = new NotificationId($this->idGenerator->generate());
        $now = new DateTimeImmutable;

        $notification = new Notification(
            id: $notificationId,
            recipientId: $recipientId,
            type: $notificationType,
            title: $title,
            body: $body,
            level: $notificationLevel,
            link: $notificationLink,
            channel: $notificationChannel,
            isRead: false,
            createdAt: $now,
            readAt: null,
        );

        $this->notificationRepository->create($notification);

        $this->eventCollector->collect(new NotificationCreated(
            notificationId: $notificationId->value,
            recipientId: $recipientId,
            type: $notificationType->value,
            title: $title,
            body: $body,
            level: $notificationLevel->value,
            link: $notificationLink?->value,
            channel: $notificationChannel->value,
            occurredAt: $now,
        ));
    }
}
