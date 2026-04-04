<?php

declare(strict_types=1);

namespace App\Domain\Label\Command\CreateLabel;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Label\Contract\Command\CreateLabel\CreateLabelCommand;
use App\Domain\Label\Contract\Event\LabelCreated;
use App\Domain\Label\Contract\Exception\LabelAlreadyExistsException;
use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\Contract\LabelRepository;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use DateTimeImmutable;

/** @implements CommandHandler<CreateLabelCommand> */
final readonly class CreateLabelHandler implements CommandHandler
{
    public function __construct(
        private LabelRepository $labelRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $labelNamespace = new LabelNamespace($command->namespace);
        $labelName = new LabelName($command->name);

        if ($this->labelRepository->findByNamespaceAndName($labelNamespace, $labelName) instanceof Label) {
            throw new LabelAlreadyExistsException($labelNamespace->value, $labelName->value);
        }

        $label = new Label(
            id: new LabelId($command->id),
            namespace: $labelNamespace,
            name: $labelName,
        );

        $this->labelRepository->create($label);

        $this->eventCollector->collect(new LabelCreated(
            labelId: $label->id->value,
            namespace: $label->namespace->value,
            name: $label->name->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
