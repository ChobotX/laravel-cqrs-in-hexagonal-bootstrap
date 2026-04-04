<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\UpdateEntry;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Registry\JsonSchemaValidator;
use App\Domain\Registry\Contract\Command\UpdateEntry\UpdateEntryCommand;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\Entry;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Contract\Event\EntryUpdated;
use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Exception\DefinitionVersionNotFoundException;
use App\Domain\Registry\Exception\EntryNotFoundException;
use App\Domain\Registry\Exception\EntryValidationException;
use App\Domain\Registry\ReferenceValidator;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateEntryCommand> */
final readonly class UpdateEntryHandler implements CommandHandler
{
    public function __construct(
        private EntryRepository $entryRepository,
        private DefinitionVersionRepository $definitionVersionRepository,
        private JsonSchemaValidator $jsonSchemaValidator,
        private SchemaSerializer $schemaSerializer,
        private EventCollector $eventCollector,
        private ReferenceValidator $referenceValidator,
    ) {}

    public function handle(Command $command): void
    {
        $entry = $this->entryRepository->findById(new EntryId($command->id))
            ?? throw new EntryNotFoundException($command->id);

        $version = $this->definitionVersionRepository->findByDefinitionAndVersion($entry->definitionId, $entry->definitionVersion)
            ?? throw new DefinitionVersionNotFoundException($entry->definitionId->value.':v'.$entry->definitionVersion->value);

        $jsonSchema = $this->schemaSerializer->toJsonSchema($version->schema);
        $errors = $this->jsonSchemaValidator->validate($command->data, $jsonSchema);

        if ($errors !== []) {
            throw new EntryValidationException($errors);
        }

        $this->referenceValidator->validate($version->schema, $command->data);

        $updatedEntry = new Entry(
            id: $entry->id,
            definitionId: $entry->definitionId,
            definitionVersion: $entry->definitionVersion,
            namespace: $entry->namespace,
            title: new EntryTitle($command->title),
            data: $command->data,
        );

        $this->entryRepository->update($updatedEntry);

        $this->eventCollector->collect(new EntryUpdated(
            entryId: $entry->id->value,
            title: $command->title,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
