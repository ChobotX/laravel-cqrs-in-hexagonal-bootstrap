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
        private JsonSchemaDeserializer $deserializer,
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
        return $this->deserializer->deserialize($jsonSchema);
    }

    /** @return array<string, mixed> */
    private function fieldToJson(SchemaField $field): array
    {
        return match (true) {
            $field instanceof StringField => $this->stringToJson($field),
            $field instanceof IntegerField => $this->integerToJson($field),
            $field instanceof NumberField => $this->numberToJson($field),
            $field instanceof BooleanField => ['type' => self::TYPE_BOOLEAN, self::X_LABEL => $field->label()],
            $field instanceof DateField => ['type' => self::TYPE_STRING, 'format' => self::FORMAT_DATE, self::X_LABEL => $field->label()],
            $field instanceof EmailField => ['type' => self::TYPE_STRING, 'format' => self::FORMAT_EMAIL, self::X_LABEL => $field->label()],
            $field instanceof ReferenceField => $this->referenceToJson($field),
            $field instanceof FileField => $this->fileToJson($field),
            $field instanceof RepeaterField => $this->repeaterToJson($field),
            $field instanceof ObjectField => $this->objectToJson($field),
            default => ['type' => self::TYPE_STRING, self::X_LABEL => $field->label()],
        };
    }

    /** @return array<string, mixed> */
    private function stringToJson(StringField $field): array
    {
        $s = ['type' => self::TYPE_STRING, self::X_LABEL => $field->label()];

        if ($field->multiline) {
            $s[self::X_MULTILINE] = true;
        }

        if ($field->minLength !== null) {
            $s['minLength'] = $field->minLength;
        }

        if ($field->maxLength !== null) {
            $s['maxLength'] = $field->maxLength;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function integerToJson(IntegerField $field): array
    {
        $s = ['type' => self::TYPE_INTEGER, self::X_LABEL => $field->label()];

        if ($field->min !== null) {
            $s['minimum'] = $field->min;
        }

        if ($field->max !== null) {
            $s['maximum'] = $field->max;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function numberToJson(NumberField $field): array
    {
        $s = ['type' => self::TYPE_NUMBER, self::X_LABEL => $field->label()];

        if ($field->min !== null) {
            $s['minimum'] = $field->min;
        }

        if ($field->max !== null) {
            $s['maximum'] = $field->max;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function referenceToJson(ReferenceField $field): array
    {
        return [
            'type' => self::TYPE_STRING,
            'format' => self::FORMAT_UUID,
            self::X_LABEL => $field->label(),
            self::X_REFERENCE => ['namespace' => $field->referenceNamespace, 'slug' => $field->referenceSlug],
        ];
    }

    /** @return array<string, mixed> */
    private function fileToJson(FileField $field): array
    {
        $s = ['type' => self::TYPE_STRING, 'format' => self::FORMAT_UUID, self::X_LABEL => $field->label(), self::X_TYPE => self::FIELD_FILE];
        $fc = [];

        if ($field->allowedMimeTypes !== null) {
            $fc['allowedMimeTypes'] = $field->allowedMimeTypes;
        }

        if ($field->maxSizeBytes !== null) {
            $fc['maxSizeBytes'] = $field->maxSizeBytes;
        }

        if ($field->fileNamespace !== null) {
            $fc['namespace'] = $field->fileNamespace;
        }

        if ($fc !== []) {
            $s[self::X_FILE] = $fc;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function repeaterToJson(RepeaterField $field): array
    {
        $items = $this->fieldsToObjectSchema($field->fields);
        $s = ['type' => self::TYPE_ARRAY, 'items' => $items, self::X_LABEL => $field->label(), self::X_TYPE => self::FIELD_REPEATER];

        if ($field->minItems > 0) {
            $s['minItems'] = $field->minItems;
        }

        if ($field->maxItems !== null) {
            $s['maxItems'] = $field->maxItems;
        }

        return $s;
    }

    /** @return array<string, mixed> */
    private function objectToJson(ObjectField $field): array
    {
        $inner = $this->fieldsToObjectSchema($field->properties);
        $inner[self::X_LABEL] = $field->label();

        return $inner;
    }

    /**
     * @param list<SchemaField> $fields
     * @return array<string, mixed>
     */
    private function fieldsToObjectSchema(array $fields): array
    {
        $properties = [];
        $required = [];

        foreach ($fields as $f) {
            $properties[$f->name()] = $this->fieldToJson($f);

            if ($f->isRequired()) {
                $required[] = $f->name();
            }
        }

        $result = ['type' => self::TYPE_OBJECT, 'properties' => $properties];

        if ($required !== []) {
            $result['required'] = $required;
        }

        return $result;
    }
}
