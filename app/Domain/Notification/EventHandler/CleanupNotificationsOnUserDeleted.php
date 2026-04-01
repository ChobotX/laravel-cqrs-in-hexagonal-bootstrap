<?php

declare(strict_types=1);

namespace App\Domain\Notification\EventHandler;

use App\Application\Event\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Event\EntityDeleted;
use App\Domain\Notification\Contract\NotificationRepository;

/** @implements DomainEventHandler<DomainEvent> */
#[RetryPolicy(tries: 3, backoff: [30, 120, 300], timeout: 60)]
final readonly class CleanupNotificationsOnUserDeleted implements DomainEventHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        if (! $domainEvent instanceof EntityDeleted) {
            return;
        }

        $this->notificationRepository->deleteAllForRecipient($domainEvent->entityId());
    }
}
