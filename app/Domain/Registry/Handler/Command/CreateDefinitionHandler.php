<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\Command\CreateDefinitionCommand;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Event\DefinitionCreated;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Exception\DefinitionAlreadyExistsException;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;
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
