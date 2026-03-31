<?php

declare(strict_types=1);

namespace App\Domain\Label\Command\RemoveLabel;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Label\Event\LabelDeleted;
use App\Domain\Label\Event\LabelRemoved;
use App\Domain\Label\LabelId;
use App\Domain\Label\LabelRepository;
use DateTimeImmutable;

/** @implements CommandHandler<RemoveLabelCommand> */
final readonly class RemoveLabelHandler implements CommandHandler
{
    public function __construct(
        private LabelRepository $labelRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $labelId = new LabelId($command->labelId);

        $this->labelRepository->removeAssignment($command->labelId, $command->labelableId);

        $this->eventCollector->collect(new LabelRemoved(
            labelId: $command->labelId,
            labelableId: $command->labelableId,
            occurredAt: new DateTimeImmutable,
        ));

        if (! $this->labelRepository->hasAssignments($labelId)) {
            $this->labelRepository->delete($labelId);

            $this->eventCollector->collect(new LabelDeleted(
                labelId: $command->labelId,
                occurredAt: new DateTimeImmutable,
            ));
        }
    }
}
