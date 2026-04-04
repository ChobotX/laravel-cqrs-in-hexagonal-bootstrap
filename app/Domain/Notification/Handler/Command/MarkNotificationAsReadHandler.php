<?php

declare(strict_types=1);

namespace App\Domain\Notification\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Notification\Contract\Command\MarkNotificationAsReadCommand;
use App\Domain\Notification\Contract\Event\NotificationRead;
use App\Domain\Notification\Contract\Notification;
use App\Domain\Notification\Contract\NotificationId;
use App\Domain\Notification\Contract\NotificationRepository;
use App\Domain\Notification\Exception\NotificationNotFoundException;
use App\Domain\Notification\Exception\NotificationOwnershipException;
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
