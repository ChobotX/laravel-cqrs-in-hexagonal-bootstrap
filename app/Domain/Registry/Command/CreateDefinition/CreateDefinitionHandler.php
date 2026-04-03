<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\CreateDefinition;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\Event\DefinitionCreated;
use App\Domain\Registry\Definition;
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
        $namespace = new DefinitionNamespace($command->namespace);
        $slug = new DefinitionSlug($command->slug);
        $name = new DefinitionName($command->name);

        if ($this->definitionRepository->findByNamespaceAndSlug($namespace, $slug) instanceof Definition) {
            throw new DefinitionAlreadyExistsException($namespace->value, $slug->value);
        }

        $definition = new Definition(
            id: new DefinitionId($command->id),
            namespace: $namespace,
            slug: $slug,
            name: $name,
        );

        $this->definitionRepository->create($definition);

        $this->eventCollector->collect(new DefinitionCreated(
            definitionId: $definition->id->value,
            namespace: $definition->namespace->value,
            slug: $definition->slug->value,
            name: $definition->name->value,
            occurredAt: new DateTimeImmutable(),
        ));
    }
}
