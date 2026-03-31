<?php

declare(strict_types=1);

namespace App\Domain\Notification\Command\MarkNotificationAsRead;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Notification\Event\NotificationRead;
use App\Domain\Notification\Exception\NotificationNotFoundException;
use App\Domain\Notification\Exception\NotificationOwnershipException;
use App\Domain\Notification\Notification;
use App\Domain\Notification\NotificationId;
use App\Domain\Notification\NotificationRepository;
use DateTimeImmutable;

/** @implements CommandHandler<MarkNotificationAsReadCommand> */
final readonly class MarkNotificationAsReadHandler implements CommandHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $notificationId = new NotificationId($command->notificationId);
        $notification = $this->notificationRepository->findById($notificationId);

        if (! $notification instanceof Notification) {
            throw new NotificationNotFoundException($command->notificationId);
        }

        if ($notification->recipientId !== $command->userId) {
            throw new NotificationOwnershipException($command->notificationId);
        }

        if ($notification->isRead) {
            return;
        }

        $now = new DateTimeImmutable;
        $this->notificationRepository->markAsRead($notificationId, $now);

        $this->eventCollector->collect(new NotificationRead(
            notificationId: $notificationId->value,
            recipientId: $command->userId,
            occurredAt: $now,
        ));
    }
}
