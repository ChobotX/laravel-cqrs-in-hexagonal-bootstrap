<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\CreateEntry;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Registry\JsonSchemaValidator;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\Entry;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Contract\Event\EntryCreated;
use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use App\Domain\Registry\Exception\EntryValidationException;
use App\Domain\Registry\Exception\NoActiveVersionException;
use App\Domain\Registry\ReferenceValidator;
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
