<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\UpdateDefinition;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\Event\DefinitionUpdated;
use App\Domain\Registry\Definition;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateDefinitionCommand> */
final readonly class UpdateDefinitionHandler implements CommandHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $definitionId = new DefinitionId($command->id);
        $definition = $this->definitionRepository->findById($definitionId);

        if (! $definition instanceof Definition) {
            throw new DefinitionNotFoundException($command->id);
        }

        $definitionName = new DefinitionName($command->name);

        $updated = new Definition(
            id: $definition->id,
            namespace: $definition->namespace,
            slug: $definition->slug,
            name: $definitionName,
        );

        $this->definitionRepository->update($updated);

        $this->eventCollector->collect(new DefinitionUpdated(
            definitionId: $definition->id->value,
            name: $definitionName->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
