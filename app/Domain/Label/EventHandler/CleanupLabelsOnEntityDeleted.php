<?php

declare(strict_types=1);

namespace App\Domain\Label\EventHandler;

use App\Application\Event\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Event\EntityDeleted;
use App\Domain\Label\Contract\Repository\LabelRepository;

/** @implements DomainEventHandler<DomainEvent> */
#[RetryPolicy(tries: 3, backoff: [30, 120, 300], timeout: 60)]
final readonly class CleanupLabelsOnEntityDeleted implements DomainEventHandler
{
    public function __construct(
        private LabelRepository $labelRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        if (! $domainEvent instanceof EntityDeleted) {
            return;
        }

        $orphanedLabelIds = $this->labelRepository->removeAllAssignmentsForLabelable(
            $domainEvent->entityId(),
        );

        if ($orphanedLabelIds !== []) {
            $this->labelRepository->deleteByIds($orphanedLabelIds);
        }
    }
}
