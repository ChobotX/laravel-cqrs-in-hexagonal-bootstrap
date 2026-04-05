<?php

declare(strict_types=1);

namespace App\Infrastructure\Registry;

use App\Domain\Registry\Contract\Service\SchemaSerializer;
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

    private const string X_PLACEHOLDER = 'x-placeholder';

    private const string X_HELP_TEXT = 'x-help-text';

    private const string X_MIN_DATE = 'x-min-date';

    private const string X_MAX_DATE = 'x-max-date';

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
            $schemaField instanceof BooleanField => $this->booleanToJson($schemaField),
            $schemaField instanceof DateField => $this->dateToJson($schemaField),
            $schemaField instanceof EmailField => $this->emailToJson($schemaField),
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

        return $this->addOptional($s, [
            'minLength' => $stringField->minLength,
            'maxLength' => $stringField->maxLength,
            'pattern' => $stringField->pattern,
            'default' => $stringField->defaultValue,
            self::X_PLACEHOLDER => $stringField->placeholder,
            self::X_HELP_TEXT => $stringField->helpText,
        ]);
    }

    /** @return array<string, mixed> */
    private function integerToJson(IntegerField $integerField): array
    {
        return $this->addOptional(['type' => self::TYPE_INTEGER, self::X_LABEL => $integerField->label()], [
            'minimum' => $integerField->min,
            'maximum' => $integerField->max,
            'multipleOf' => $integerField->step,
            'default' => $integerField->defaultValue,
            self::X_PLACEHOLDER => $integerField->placeholder,
            self::X_HELP_TEXT => $integerField->helpText,
        ]);
    }

    /** @return array<string, mixed> */
    private function numberToJson(NumberField $numberField): array
    {
        return $this->addOptional(['type' => self::TYPE_NUMBER, self::X_LABEL => $numberField->label()], [
            'minimum' => $numberField->min,
            'maximum' => $numberField->max,
            'multipleOf' => $numberField->step,
            'default' => $numberField->defaultValue,
            self::X_PLACEHOLDER => $numberField->placeholder,
            self::X_HELP_TEXT => $numberField->helpText,
        ]);
    }

    /** @return array<string, mixed> */
    private function booleanToJson(BooleanField $booleanField): array
    {
        return $this->addOptional(['type' => self::TYPE_BOOLEAN, self::X_LABEL => $booleanField->label()], [
            'default' => $booleanField->defaultValue,
            self::X_HELP_TEXT => $booleanField->helpText,
        ]);
    }

    /** @return array<string, mixed> */
    private function dateToJson(DateField $dateField): array
    {
        return $this->addOptional(
            ['type' => self::TYPE_STRING, 'format' => self::FORMAT_DATE, self::X_LABEL => $dateField->label()],
            [
                self::X_MIN_DATE => $dateField->minDate,
                self::X_MAX_DATE => $dateField->maxDate,
                'default' => $dateField->defaultValue,
                self::X_PLACEHOLDER => $dateField->placeholder,
                self::X_HELP_TEXT => $dateField->helpText,
            ],
        );
    }

    /** @return array<string, mixed> */
    private function emailToJson(EmailField $emailField): array
    {
        return $this->addOptional(
            ['type' => self::TYPE_STRING, 'format' => self::FORMAT_EMAIL, self::X_LABEL => $emailField->label()],
            [self::X_PLACEHOLDER => $emailField->placeholder, self::X_HELP_TEXT => $emailField->helpText],
        );
    }

    /** @return array<string, mixed> */
    private function referenceToJson(ReferenceField $referenceField): array
    {
        return $this->addOptional([
            'type' => self::TYPE_STRING,
            'format' => self::FORMAT_UUID,
            self::X_LABEL => $referenceField->label(),
            self::X_REFERENCE => ['namespace' => $referenceField->referenceNamespace, 'slug' => $referenceField->referenceSlug],
        ], [self::X_PLACEHOLDER => $referenceField->placeholder, self::X_HELP_TEXT => $referenceField->helpText]);
    }

    /** @return array<string, mixed> */
    private function fileToJson(FileField $fileField): array
    {
        $s = ['type' => self::TYPE_STRING, 'format' => self::FORMAT_UUID, self::X_LABEL => $fileField->label(), self::X_TYPE => self::FIELD_FILE];
        $fc = array_filter([
            'allowedMimeTypes' => $fileField->allowedMimeTypes,
            'maxSizeBytes' => $fileField->maxSizeBytes,
            'namespace' => $fileField->fileNamespace,
        ], static fn (mixed $v): bool => $v !== null);

        if ($fc !== []) {
            $s[self::X_FILE] = $fc;
        }

        return $this->addOptional($s, [self::X_HELP_TEXT => $fileField->helpText]);
    }

    /** @return array<string, mixed> */
    private function repeaterToJson(RepeaterField $repeaterField): array
    {
        $s = ['type' => self::TYPE_ARRAY, 'items' => $this->fieldsToObjectSchema($repeaterField->fields), self::X_LABEL => $repeaterField->label(), self::X_TYPE => self::FIELD_REPEATER];

        if ($repeaterField->minItems > 0) {
            $s['minItems'] = $repeaterField->minItems;
        }

        return $this->addOptional($s, [
            'maxItems' => $repeaterField->maxItems,
            self::X_HELP_TEXT => $repeaterField->helpText,
        ]);
    }

    /** @return array<string, mixed> */
    private function objectToJson(ObjectField $objectField): array
    {
        $inner = $this->fieldsToObjectSchema($objectField->properties);
        $inner[self::X_LABEL] = $objectField->label();

        return $this->addOptional($inner, [self::X_HELP_TEXT => $objectField->helpText]);
    }

    /**
     * @param  array<string, mixed>  $s
     * @param  array<string, mixed>  $optional
     * @return array<string, mixed>
     */
    private function addOptional(array $s, array $optional): array
    {
        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $s[$key] = $value;
            }
        }

        return $s;
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
