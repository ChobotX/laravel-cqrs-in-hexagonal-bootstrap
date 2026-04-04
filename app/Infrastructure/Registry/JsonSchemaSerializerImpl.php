<?php

declare(strict_types=1);

namespace App\Infrastructure\Registry;

use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\Schema\BooleanField;
use App\Domain\Registry\Schema\DateField;
use App\Domain\Registry\Schema\EmailField;
use App\Domain\Registry\Schema\FileField;
use App\Domain\Registry\Schema\IntegerField;
use App\Domain\Registry\Schema\NumberField;
use App\Domain\Registry\Schema\ObjectField;
use App\Domain\Registry\Schema\ReferenceField;
use App\Domain\Registry\Schema\RepeaterField;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\SchemaField;
use App\Domain\Registry\Schema\StringField;

final readonly class JsonSchemaSerializerImpl implements SchemaSerializer
{
    private const string TYPE_STRING = 'string';

    private const string TYPE_INTEGER = 'integer';

    private const string TYPE_NUMBER = 'number';

    private const string TYPE_BOOLEAN = 'boolean';

    private const string TYPE_OBJECT = 'object';

    private const string TYPE_ARRAY = 'array';

    private const string FORMAT_DATE = 'date';

    private const string FORMAT_EMAIL = 'email';

    private const string FORMAT_UUID = 'uuid';

    private const string X_LABEL = 'x-field-label';

    private const string X_TYPE = 'x-field-type';

    private const string X_REFERENCE = 'x-reference';

    private const string X_MULTILINE = 'x-multiline';

    private const string X_FILE = 'x-file';

    private const string FIELD_FILE = 'file';

    private const string FIELD_REPEATER = 'repeater';

    public function __construct(
        private JsonSchemaDeserializer $jsonSchemaDeserializer,
    ) {}

    public function toJsonSchema(Schema $schema): array
    {
        $properties = [];
        $required = [];

        foreach ($schema->fields as $field) {
            $properties[$field->name()] = $this->fieldToJson($field);

            if ($field->isRequired()) {
                $required[] = $field->name();
            }
        }

        $result = [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => self::TYPE_OBJECT,
            'properties' => $properties,
        ];

        if ($required !== []) {
            $result['required'] = $required;
        }

        return $result;
    }

    public function fromJsonSchema(array $jsonSchema): Schema
    {
        return $this->jsonSchemaDeserializer->deserialize($jsonSchema);
    }

    /** @return array<string, mixed> */
    private function fieldToJson(SchemaField $schemaField): array
    {
        return match (true) {
            $schemaField instanceof StringField => $this->stringToJson($schemaField),
            $schemaField instanceof IntegerField => $this->integerToJson($schemaField),
            $schemaField instanceof NumberField => $this->numberToJson($schemaField),
            $schemaField instanceof BooleanField => ['type' => self::TYPE_BOOLEAN, self::X_LABEL => $schemaField->label()],
            $schemaField instanceof DateField => ['type' => self::TYPE_STRING, 'format' => self::FORMAT_DATE, self::X_LABEL => $schemaField->label()],
            $schemaField instanceof EmailField => ['type' => self::TYPE_STRING, 'format' => self::FORMAT_EMAIL, self::X_LABEL => $schemaField->label()],
            $schemaField instanceof ReferenceField => $this->referenceToJson($schemaField),
            $schemaField instanceof FileField => $this->fileToJson($schemaField),
            $schemaField instanceof RepeaterField => $this->repeaterToJson($schemaField),
            $schemaField instanceof ObjectField => $this->objectToJson($schemaField),
            default => ['type' => self::TYPE_STRING, self::X_LABEL => $schemaField->label()],
        };
    }

    /** @return array<string, mixed> */
    private function stringToJson(StringField $stringField): array
    {
        $s = ['type' => self::TYPE_STRING, self::X_LABEL => $stringField->label()];

        if ($stringField->multiline) {
            $s[self::X_MULTILINE] = true;
        }

        if ($stringField->minLength !== null) {
            $s['minLength'] = $stringField->minLength;
        }

        if ($stringField->maxLength !== null) {
            $s['maxLength'] = $stringField->maxLength;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function integerToJson(IntegerField $integerField): array
    {
        $s = ['type' => self::TYPE_INTEGER, self::X_LABEL => $integerField->label()];

        if ($integerField->min !== null) {
            $s['minimum'] = $integerField->min;
        }

        if ($integerField->max !== null) {
            $s['maximum'] = $integerField->max;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function numberToJson(NumberField $numberField): array
    {
        $s = ['type' => self::TYPE_NUMBER, self::X_LABEL => $numberField->label()];

        if ($numberField->min !== null) {
            $s['minimum'] = $numberField->min;
        }

        if ($numberField->max !== null) {
            $s['maximum'] = $numberField->max;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function referenceToJson(ReferenceField $referenceField): array
    {
        return [
            'type' => self::TYPE_STRING,
            'format' => self::FORMAT_UUID,
            self::X_LABEL => $referenceField->label(),
            self::X_REFERENCE => ['namespace' => $referenceField->referenceNamespace, 'slug' => $referenceField->referenceSlug],
        ];
    }

    /** @return array<string, mixed> */
    private function fileToJson(FileField $fileField): array
    {
        $s = ['type' => self::TYPE_STRING, 'format' => self::FORMAT_UUID, self::X_LABEL => $fileField->label(), self::X_TYPE => self::FIELD_FILE];
        $fc = [];

        if ($fileField->allowedMimeTypes !== null) {
            $fc['allowedMimeTypes'] = $fileField->allowedMimeTypes;
        }

        if ($fileField->maxSizeBytes !== null) {
            $fc['maxSizeBytes'] = $fileField->maxSizeBytes;
        }

        if ($fileField->fileNamespace !== null) {
            $fc['namespace'] = $fileField->fileNamespace;
        }

        if ($fc !== []) {
            $s[self::X_FILE] = $fc;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function repeaterToJson(RepeaterField $repeaterField): array
    {
        $items = $this->fieldsToObjectSchema($repeaterField->fields);
        $s = ['type' => self::TYPE_ARRAY, 'items' => $items, self::X_LABEL => $repeaterField->label(), self::X_TYPE => self::FIELD_REPEATER];

        if ($repeaterField->minItems > 0) {
            $s['minItems'] = $repeaterField->minItems;
        }

        if ($repeaterField->maxItems !== null) {
            $s['maxItems'] = $repeaterField->maxItems;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function objectToJson(ObjectField $objectField): array
    {
        $inner = $this->fieldsToObjectSchema($objectField->properties);
        $inner[self::X_LABEL] = $objectField->label();

        return $inner;
    }

    /**
     * @param  list<SchemaField>  $fields
     * @return array<string, mixed>
     */
    private function fieldsToObjectSchema(array $fields): array
    {
        $properties = [];
        $required = [];

        foreach ($fields as $field) {
            $properties[$field->name()] = $this->fieldToJson($field);

            if ($field->isRequired()) {
                $required[] = $field->name();
            }
        }

        $result = ['type' => self::TYPE_OBJECT, 'properties' => $properties];

        if ($required !== []) {
            $result['required'] = $required;
        }

        return $result;
    }
}
