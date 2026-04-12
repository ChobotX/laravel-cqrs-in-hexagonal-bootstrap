<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Command;

use App\Application\Event\PropertyChangeBuilder;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Constant\EntryFields;
use App\Domain\Registry\Contract\Command\UpdateEntryCommand;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Event\EntryUpdated;
use App\Domain\Registry\Contract\Repository\DefinitionVersionRepository;
use App\Domain\Registry\Contract\Repository\EntryRepository;
use App\Domain\Registry\Contract\Service\JsonSchemaValidator;
use App\Domain\Registry\Contract\Service\SchemaSerializer;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\Exception\DefinitionVersionNotFoundException;
use App\Domain\Registry\Exception\EntryNotFoundException;
use App\Domain\Registry\Exception\EntryValidationException;
use App\Domain\Registry\Service\ReferenceValidator;
use App\Domain\Registry\ValueObject\EntryTitle;
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
        private PropertyChangeBuilder $propertyChangeBuilder,
    ) {}

    public function handle(Command $command): void
    {
        $entry = $this->entryRepository->findById(new EntryId($command->id))
            ?? throw new EntryNotFoundException($command->id);

        $changes = $this->propertyChangeBuilder->diff([
            EntryFields::TITLE => [$entry->title->value, $command->title],
            EntryFields::DATA => [$this->encodeData($entry->data), $this->encodeData($command->data)],
        ]);

        if ($changes === []) {
            return;
        }

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
            createdByUserId: $entry->createdByUserId,
        );

        $this->entryRepository->update($updatedEntry);

        $this->eventCollector->collect(new EntryUpdated(
            entryId: $entry->id->value,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        ));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function encodeData(array $data): string
    {
        return (string) json_encode($data);
    }
}
