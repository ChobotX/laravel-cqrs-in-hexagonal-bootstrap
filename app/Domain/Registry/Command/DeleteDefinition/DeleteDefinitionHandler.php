<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\DeleteDefinition;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Contract\Event\DefinitionDeleted;
use App\Domain\Registry\Definition;
use App\Domain\Registry\Exception\DefinitionHasEntriesException;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteDefinitionCommand> */
final readonly class DeleteDefinitionHandler implements CommandHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
        private EntryRepository $entryRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $definitionId = new DefinitionId($command->id);
        $definition = $this->definitionRepository->findById($definitionId);

        if (! $definition instanceof Definition) {
            throw new DefinitionNotFoundException($command->id);
        }

        if ($this->entryRepository->existsByDefinition($definition->id)) {
            throw new DefinitionHasEntriesException($definition->id->value);
        }

        $this->definitionRepository->delete($definition->id);

        $this->eventCollector->collect(new DefinitionDeleted(
            definitionId: $definition->id->value,
            occurredAt: new DateTimeImmutable(),
        ));
    }
}
