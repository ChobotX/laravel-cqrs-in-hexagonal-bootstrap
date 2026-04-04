<?php

declare(strict_types=1);

namespace App\Domain\Notification\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Notification\Contract\Command\DeleteNotificationCommand;
use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\Event\NotificationDeleted;
use App\Domain\Notification\Contract\Repository\NotificationRepository;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use App\Domain\Notification\Exception\NotificationNotFoundException;
use App\Domain\Notification\Exception\NotificationOwnershipException;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteNotificationCommand> */
final readonly class DeleteNotificationHandler implements CommandHandler
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

        $this->notificationRepository->delete($notificationId);

        $this->eventCollector->collect(new NotificationDeleted(
            notificationId: $notificationId->value,
            recipientId: $command->userId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
