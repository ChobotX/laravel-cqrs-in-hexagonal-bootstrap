<?php

declare(strict_types=1);

namespace App\Domain\Notification\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Notification\Contract\Command\MarkAllNotificationsAsReadCommand;
use App\Domain\Notification\Contract\Event\AllNotificationsRead;
use App\Domain\Notification\Contract\NotificationRepository;
use DateTimeImmutable;

/** @implements CommandHandler<MarkAllNotificationsAsReadCommand> */
final readonly class MarkAllNotificationsAsReadHandler implements CommandHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        if ($this->notificationRepository->countUnreadByRecipient($command->userId) === 0) {
            return;
        }

        $now = new DateTimeImmutable;
        $this->notificationRepository->markAllAsReadForRecipient($command->userId, $now);

        $this->eventCollector->collect(new AllNotificationsRead(
            recipientId: $command->userId,
            occurredAt: $now,
        ));
    }
}
