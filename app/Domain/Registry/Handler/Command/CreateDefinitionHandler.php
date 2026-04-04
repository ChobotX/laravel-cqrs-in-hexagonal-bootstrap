<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\Command\CreateDefinitionCommand;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\Event\DefinitionCreated;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Exception\DefinitionAlreadyExistsException;
use DateTimeImmutable;

/** @implements CommandHandler<CreateDefinitionCommand> */
final readonly class CreateDefinitionHandler implements CommandHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $definitionNamespace = new DefinitionNamespace($command->namespace);
        $definitionSlug = new DefinitionSlug($command->slug);
        $definitionName = new DefinitionName($command->name);

        if ($this->definitionRepository->findByNamespaceAndSlug($definitionNamespace, $definitionSlug) instanceof Definition) {
            throw new DefinitionAlreadyExistsException($definitionNamespace->value, $definitionSlug->value);
        }

        $definition = new Definition(
            id: new DefinitionId($command->id),
            namespace: $definitionNamespace,
            slug: $definitionSlug,
            name: $definitionName,
        );

        $this->definitionRepository->create($definition);

        $this->eventCollector->collect(new DefinitionCreated(
            definitionId: $definition->id->value,
            namespace: $definition->namespace->value,
            slug: $definition->slug->value,
            name: $definition->name->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
