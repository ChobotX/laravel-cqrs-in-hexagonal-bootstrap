<?php

declare(strict_types=1);

namespace App\Domain\Registry;

use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Exception\InvalidReferenceException;
use App\Domain\Registry\Schema\ObjectField;
use App\Domain\Registry\Schema\ReferenceField;
use App\Domain\Registry\Schema\RepeaterField;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\SchemaField;

final readonly class ReferenceValidator
{
    public function __construct(
        private EntryRepository $entryRepository,
    ) {}

    /** @param array<string, mixed> $data */
    public function validate(Schema $schema, array $data): void
    {
        $this->validateFieldReferences($schema->fields, $data);
    }

    /**
     * @param  list<SchemaField>  $fields
     * @param  array<string, mixed>  $data
     */
    private function validateFieldReferences(array $fields, array $data): void
    {
        foreach ($fields as $field) {
            $this->validateSingleFieldReference($field, $data);
        }
    }

    /** @param array<string, mixed> $data */
    private function validateSingleFieldReference(SchemaField $schemaField, array $data): void
    {
        if ($schemaField instanceof ReferenceField) {
            $this->validateReferenceValue($schemaField, $data);

            return;
        }

        if ($schemaField instanceof RepeaterField) {
            $this->validateRepeaterReferences($schemaField, $data);

            return;
        }

        if ($schemaField instanceof ObjectField) {
            $this->validateObjectReferences($schemaField, $data);
        }
    }

    /** @param array<string, mixed> $data */
    private function validateReferenceValue(ReferenceField $referenceField, array $data): void
    {
        $value = $data[$referenceField->name()] ?? null;

        if (! is_string($value) || $value === '') {
            return;
        }

        if (! $this->entryRepository->findById(new EntryId($value)) instanceof Entry) {
            throw new InvalidReferenceException($referenceField->name(), $value);
        }
    }

    /** @param array<string, mixed> $data */
    private function validateRepeaterReferences(RepeaterField $repeaterField, array $data): void
    {
        $items = $data[$repeaterField->name()] ?? [];

        if (! is_array($items)) {
            return;
        }

        foreach ($items as $item) {
            if (is_array($item)) {
                /** @var array<string, mixed> $typedItem */
                $typedItem = $item;
                $this->validateFieldReferences($repeaterField->fields, $typedItem);
            }
        }
    }

    /** @param array<string, mixed> $data */
    private function validateObjectReferences(ObjectField $objectField, array $data): void
    {
        $nested = $data[$objectField->name()] ?? [];

        if (is_array($nested)) {
            /** @var array<string, mixed> $typedNested */
            $typedNested = $nested;
            $this->validateFieldReferences($objectField->properties, $typedNested);
        }
    }
}
