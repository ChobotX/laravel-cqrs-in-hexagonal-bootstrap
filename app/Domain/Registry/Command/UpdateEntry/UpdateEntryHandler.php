<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\UpdateEntry;

use App\Domain\Registry\Schema\ObjectField;
use App\Domain\Registry\Schema\ReferenceField;
use App\Domain\Registry\Schema\RepeaterField;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\SchemaField;
use App\Domain\Registry\Contract\SchemaSerializer;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Registry\JsonSchemaValidator;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Contract\Event\EntryUpdated;
use App\Domain\Registry\Entry;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Exception\DefinitionVersionNotFoundException;
use App\Domain\Registry\Exception\EntryNotFoundException;
use App\Domain\Registry\Exception\EntryValidationException;
use App\Domain\Registry\Exception\InvalidReferenceException;
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
    ) {}

    public function handle(Command $command): void
    {
        $entry = $this->entryRepository->findById(new EntryId($command->id))
            ?? throw new EntryNotFoundException($command->id);

        $version = $this->definitionVersionRepository->findByDefinitionAndVersion($entry->definitionId, $entry->definitionVersion)
            ?? throw new DefinitionVersionNotFoundException($entry->definitionId->value . ':v' . $entry->definitionVersion->value);

        $jsonSchema = $this->schemaSerializer->toJsonSchema($version->schema);
        $errors = $this->jsonSchemaValidator->validate($command->data, $jsonSchema);

        if ($errors !== []) {
            throw new EntryValidationException($errors);
        }

        $this->validateReferences($version->schema, $command->data);

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
            occurredAt: new DateTimeImmutable(),
        ));
    }

    /** @param array<string, mixed> $data */
    private function validateReferences(Schema $schema, array $data): void
    {
        $this->validateFieldReferences($schema->fields, $data);
    }

    /**
     * @param list<SchemaField> $fields
     * @param array<string, mixed> $data
     */
    private function validateFieldReferences(array $fields, array $data): void
    {
        foreach ($fields as $field) {
            $this->validateSingleFieldReference($field, $data);
        }
    }

    /** @param array<string, mixed> $data */
    private function validateSingleFieldReference(SchemaField $field, array $data): void
    {
        if ($field instanceof ReferenceField) {
            $this->validateReferenceValue($field, $data);

            return;
        }

        if ($field instanceof RepeaterField) {
            $this->validateRepeaterReferences($field, $data);

            return;
        }

        if ($field instanceof ObjectField) {
            $this->validateObjectReferences($field, $data);
        }
    }

    /** @param array<string, mixed> $data */
    private function validateReferenceValue(ReferenceField $field, array $data): void
    {
        $value = $data[$field->name()] ?? null;

        if (! is_string($value) || $value === '') {
            return;
        }

        if (! $this->entryRepository->findById(new EntryId($value)) instanceof Entry) {
            throw new InvalidReferenceException($field->name(), $value);
        }
    }

    /** @param array<string, mixed> $data */
    private function validateRepeaterReferences(RepeaterField $field, array $data): void
    {
        $items = $data[$field->name()] ?? [];

        if (! is_array($items)) {
            return;
        }

        foreach ($items as $item) {
            if (is_array($item)) {
                /** @var array<string, mixed> $item */
                $this->validateFieldReferences($field->fields, $item);
            }
        }
    }

    /** @param array<string, mixed> $data */
    private function validateObjectReferences(ObjectField $field, array $data): void
    {
        $nested = $data[$field->name()] ?? [];

        if (is_array($nested)) {
            /** @var array<string, mixed> $nested */
            $this->validateFieldReferences($field->properties, $nested);
        }
    }
}
