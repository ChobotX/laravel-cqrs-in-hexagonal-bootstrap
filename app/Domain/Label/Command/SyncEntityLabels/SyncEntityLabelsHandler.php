<?php

declare(strict_types=1);

namespace App\Domain\Label\Command\SyncEntityLabels;

use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Label\Contract\Event\LabelAssigned;
use App\Domain\Label\Contract\Event\LabelDeleted;
use App\Domain\Label\Contract\Event\LabelRemoved;
use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\Contract\LabelRepository;
use App\Domain\Label\Label;
use DateTimeImmutable;

use function array_diff;
use function array_map;

/** @implements CommandHandler<SyncEntityLabelsCommand> */
final readonly class SyncEntityLabelsHandler implements CommandHandler
{
    private const string REQUIRED_PERMISSION = 'labels.management.read';

    public function __construct(
        private LabelRepository $labelRepository,
        private AuthorizationChecker $authorizationChecker,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        if ($command->submittedLabelIds === null) {
            return;
        }

        if (! $this->authorizationChecker->can($command->actingUserId, self::REQUIRED_PERMISSION)) {
            return;
        }

        $currentLabels = $this->labelRepository->findByLabelableId($command->entityId);
        $currentLabelIds = array_map(fn (Label $label): string => $label->id->value, $currentLabels);

        $toAdd = array_diff($command->submittedLabelIds, $currentLabelIds);
        $toRemove = array_diff($currentLabelIds, $command->submittedLabelIds);

        foreach ($toAdd as $labelId) {
            $this->labelRepository->assignLabel($labelId, $command->entityId);
            $this->eventCollector->collect(new LabelAssigned(
                labelId: $labelId,
                labelableId: $command->entityId,
                occurredAt: new DateTimeImmutable,
            ));
        }

        foreach ($toRemove as $labelId) {
            $this->labelRepository->removeAssignment($labelId, $command->entityId);
            $this->eventCollector->collect(new LabelRemoved(
                labelId: $labelId,
                labelableId: $command->entityId,
                occurredAt: new DateTimeImmutable,
            ));

            $labelIdVo = new LabelId($labelId);

            if (! $this->labelRepository->hasAssignments($labelIdVo)) {
                $this->labelRepository->delete($labelIdVo);
                $this->eventCollector->collect(new LabelDeleted(
                    labelId: $labelId,
                    occurredAt: new DateTimeImmutable,
                ));
            }
        }
    }
}
