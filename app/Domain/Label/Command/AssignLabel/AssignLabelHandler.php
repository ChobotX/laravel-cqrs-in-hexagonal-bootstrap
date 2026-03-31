<?php

declare(strict_types=1);

namespace App\Domain\Label\Command\AssignLabel;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Label\Event\LabelAssigned;
use App\Domain\Label\Exception\LabelNamespaceMismatchException;
use App\Domain\Label\Exception\LabelNotFoundException;
use App\Domain\Label\Label;
use App\Domain\Label\LabelId;
use App\Domain\Label\LabelRepository;
use DateTimeImmutable;

/** @implements CommandHandler<AssignLabelCommand> */
final readonly class AssignLabelHandler implements CommandHandler
{
    public function __construct(
        private LabelRepository $labelRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $labelId = new LabelId($command->labelId);
        $label = $this->labelRepository->findById($labelId);

        if (! $label instanceof Label) {
            throw new LabelNotFoundException($command->labelId);
        }

        if ($label->namespace->value !== $command->expectedNamespace) {
            throw new LabelNamespaceMismatchException(
                $command->labelId,
                $command->expectedNamespace,
                $label->namespace->value,
            );
        }

        $this->labelRepository->assignLabel($command->labelId, $command->labelableId);

        $this->eventCollector->collect(new LabelAssigned(
            labelId: $command->labelId,
            labelableId: $command->labelableId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
