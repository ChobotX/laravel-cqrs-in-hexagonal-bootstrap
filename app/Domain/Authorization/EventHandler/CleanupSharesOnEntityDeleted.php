<?php

declare(strict_types=1);

namespace App\Domain\Authorization\EventHandler;

use App\Application\Event\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Event\EntityDeleted;
use App\Domain\Authorization\Contract\Repository\RecordShareRepository;

/** @implements DomainEventHandler<DomainEvent> */
#[RetryPolicy(tries: 3, backoff: [30, 120, 300], timeout: 60)]
final readonly class CleanupSharesOnEntityDeleted implements DomainEventHandler
{
    public function __construct(
        private RecordShareRepository $recordShareRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        if (! $domainEvent instanceof EntityDeleted) {
            return;
        }

        $this->recordShareRepository->revokeAllForResource(
            $domainEvent->entityType(),
            $domainEvent->entityId(),
        );
    }
}
