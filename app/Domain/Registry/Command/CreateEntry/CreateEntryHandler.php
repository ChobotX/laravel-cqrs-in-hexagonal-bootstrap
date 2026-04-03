<?php

declare(strict_types=1);

namespace App\Domain\Registry\Command\CreateEntry;

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
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Contract\Event\EntryCreated;
use App\Domain\Registry\Entry;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Exception\DefinitionNotFoundException;
use App\Domain\Registry\Exception\EntryValidationException;
use App\Domain\Registry\Exception\InvalidReferenceException;
use App\Domain\Registry\Exception\NoActiveVersionException;
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

        $this->validateReferences($version->schema, $command->data);

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
