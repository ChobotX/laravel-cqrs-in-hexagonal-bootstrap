<?php

declare(strict_types=1);

namespace App\Domain\Label\EventHandler;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Event\EntityDeleted;
use App\Domain\Label\LabelRepository;

/** @implements DomainEventHandler<DomainEvent> */
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
