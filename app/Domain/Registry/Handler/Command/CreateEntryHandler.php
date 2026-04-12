<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\Command\CreateEntryCommand;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Event\EntryCreated;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;
use App\Domain\Registry\Contract\Repository\DefinitionVersionRepository;
use App\Domain\Registry\Contract\Repository\EntryRepository;
use App\Domain\Registry\Contract\Service\JsonSchemaValidator;
use App\Domain\Registry\Contract\Service\SchemaSerializer;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use App\Domain\Registry\Exception\EntryValidationException;
use App\Domain\Registry\Exception\NoActiveVersionException;
use App\Domain\Registry\Service\ReferenceValidator;
use App\Domain\Registry\ValueObject\EntryTitle;
use DateTimeImmutable;

/** @implements CommandHandler<CreateEntryCommand> */
final readonly class CreateEntryHandler implements CommandHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
        private DefinitionVersionRepository $definitionVersionRepository,
        private EntryRepository $entryRepository,
        private JsonSchemaValidator $jsonSchemaValidator,
        private SchemaSerializer $schemaSerializer,
        private EventCollector $eventCollector,
        private ReferenceValidator $referenceValidator,
    ) {}

    public function handle(Command $command): void
    {
        $definition = $this->definitionRepository->findById(new DefinitionId($command->definitionId))
            ?? throw new DefinitionNotFoundException($command->definitionId);

        $version = $this->definitionVersionRepository->findActiveByDefinition($definition->id)
            ?? throw new NoActiveVersionException($command->definitionId);

        $jsonSchema = $this->schemaSerializer->toJsonSchema($version->schema);
        $errors = $this->jsonSchemaValidator->validate($command->data, $jsonSchema);

        if ($errors !== []) {
            throw new EntryValidationException($errors);
        }

        $this->referenceValidator->validate($version->schema, $command->data);

        $entry = new Entry(
            id: new EntryId($command->id),
            definitionId: $definition->id,
            definitionVersion: $version->version,
            namespace: $definition->namespace,
            title: new EntryTitle($command->title),
            data: $command->data,
            createdByUserId: $command->createdByUserId,
        );

        $this->entryRepository->create($entry);

        $this->eventCollector->collect(new EntryCreated(
            entryId: $entry->id->value,
            definitionId: $definition->id->value,
            definitionVersion: $version->version->value,
            namespace: $definition->namespace->value,
            title: $entry->title->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
