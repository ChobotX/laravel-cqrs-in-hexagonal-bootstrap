<?php

declare(strict_types=1);

namespace App\Infrastructure\Registry;

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

final readonly class JsonSchemaDeserializer
{
    private const string TYPE_STRING = 'string';

    private const string TYPE_INTEGER = 'integer';

    private const string TYPE_NUMBER = 'number';

    private const string TYPE_BOOLEAN = 'boolean';

    private const string TYPE_OBJECT = 'object';

    private const string TYPE_ARRAY = 'array';

    private const string FORMAT_DATE = 'date';

    private const string FORMAT_EMAIL = 'email';

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

    /** @param array<string, mixed> $jsonSchema */
    public function deserialize(array $jsonSchema): Schema
    {
        /** @var array<string, array<string, mixed>> $properties */
        $properties = $jsonSchema['properties'] ?? [];
        /** @var list<string> $required */
        $required = $jsonSchema['required'] ?? [];

        $fields = [];

        foreach ($properties as $name => $fieldSchema) {
            $fields[] = $this->toField($name, $fieldSchema, in_array($name, $required, true));
        }

        return new Schema($fields);
    }

    /** @param array<string, mixed> $s */
    private function toField(string $name, array $s, bool $required): SchemaField
    {
        if (isset($s[self::X_REFERENCE]) && is_array($s[self::X_REFERENCE])) {
            return $this->toReference($name, $s, $required);
        }

        $xType = $this->str($s, self::X_TYPE);

        if ($xType === self::FIELD_FILE) {
            return $this->toFile($name, $s, $required);
        }

        if ($xType === self::FIELD_REPEATER) {
            return $this->toRepeaterField($name, $s, $required);
        }

        return $this->toStandard($name, $s, $required);
    }

    /** @param array<string, mixed> $s */
    private function toReference(string $name, array $s, bool $required): ReferenceField
    {
        /** @var array{namespace?: string, slug?: string} $ref */
        $ref = $s[self::X_REFERENCE];

        return new ReferenceField(
            name: $name,
            label: $this->str($s, self::X_LABEL, $name),
            required: $required,
            referenceNamespace: $ref['namespace'] ?? '',
            referenceSlug: $ref['slug'] ?? '',
            placeholder: $this->nullableStr($s, self::X_PLACEHOLDER),
            helpText: $this->nullableStr($s, self::X_HELP_TEXT),
        );
    }

    /** @param array<string, mixed> $s */
    private function toFile(string $name, array $s, bool $required): FileField
    {
        /** @var array{allowedMimeTypes?: list<string>, maxSizeBytes?: int, namespace?: string} $fc */
        $fc = is_array($s[self::X_FILE] ?? null) ? $s[self::X_FILE] : [];

        return new FileField(
            name: $name,
            label: $this->str($s, self::X_LABEL, $name),
            required: $required,
            allowedMimeTypes: $fc['allowedMimeTypes'] ?? null,
            maxSizeBytes: $fc['maxSizeBytes'] ?? null,
            fileNamespace: $fc['namespace'] ?? null,
            helpText: $this->nullableStr($s, self::X_HELP_TEXT),
        );
    }

    /** @param array<string, mixed> $s */
    private function toRepeaterField(string $name, array $s, bool $required): RepeaterField
    {
        return new RepeaterField(
            name: $name,
            label: $this->str($s, self::X_LABEL, $name),
            required: $required,
            fields: $this->itemFields($s),
            minItems: $this->int($s, 'minItems', 0),
            maxItems: $this->nullableInt($s, 'maxItems'),
            helpText: $this->nullableStr($s, self::X_HELP_TEXT),
        );
    }

    /** @param array<string, mixed> $s */
    private function toStandard(string $name, array $s, bool $required): SchemaField
    {
        $type = $this->str($s, 'type', self::TYPE_STRING);
        $label = $this->str($s, self::X_LABEL, $name);

        if ($type === self::TYPE_STRING) {
            return $this->toStringType($name, $label, $s, $required);
        }

        return match ($type) {
            self::TYPE_INTEGER => new IntegerField(
                $name,
                $label,
                $required,
                $this->nullableInt($s, 'minimum'),
                $this->nullableInt($s, 'maximum'),
                $this->nullableInt($s, 'multipleOf'),
                $this->nullableStr($s, self::X_PLACEHOLDER),
                $this->nullableStr($s, self::X_HELP_TEXT),
                $this->nullableInt($s, 'default'),
            ),
            self::TYPE_NUMBER => new NumberField(
                $name,
                $label,
                $required,
                $this->nullableFloat($s, 'minimum'),
                $this->nullableFloat($s, 'maximum'),
                $this->nullableFloat($s, 'multipleOf'),
                $this->nullableStr($s, self::X_PLACEHOLDER),
                $this->nullableStr($s, self::X_HELP_TEXT),
                $this->nullableFloat($s, 'default'),
            ),
            self::TYPE_BOOLEAN => new BooleanField(
                $name,
                $label,
                $required,
                $this->nullableStr($s, self::X_HELP_TEXT),
                $this->nullableBool($s, 'default'),
            ),
            self::TYPE_OBJECT => $this->toObject($name, $s, $required),
            self::TYPE_ARRAY => $this->toRepeaterField($name, $s, $required),
            default => new StringField($name, $label, $required),
        };
    }

    /** @param array<string, mixed> $s */
    private function toStringType(string $name, string $label, array $s, bool $required): SchemaField
    {
        $format = isset($s['format']) && is_string($s['format']) ? $s['format'] : null;

        return match ($format) {
            self::FORMAT_DATE => new DateField(
                $name,
                $label,
                $required,
                $this->nullableStr($s, self::X_MIN_DATE),
                $this->nullableStr($s, self::X_MAX_DATE),
                $this->nullableStr($s, self::X_PLACEHOLDER),
                $this->nullableStr($s, self::X_HELP_TEXT),
                $this->nullableStr($s, 'default'),
            ),
            self::FORMAT_EMAIL => new EmailField(
                $name,
                $label,
                $required,
                $this->nullableStr($s, self::X_PLACEHOLDER),
                $this->nullableStr($s, self::X_HELP_TEXT),
            ),
            default => $this->toStringField($name, $label, $s, $required),
        };
    }

    /** @param array<string, mixed> $s */
    private function toStringField(string $name, string $label, array $s, bool $required): StringField
    {
        return new StringField(
            name: $name,
            label: $label,
            required: $required,
            multiline: isset($s[self::X_MULTILINE]) && $s[self::X_MULTILINE] === true,
            minLength: $this->nullableInt($s, 'minLength'),
            maxLength: $this->nullableInt($s, 'maxLength'),
            pattern: $this->nullableStr($s, 'pattern'),
            placeholder: $this->nullableStr($s, self::X_PLACEHOLDER),
            helpText: $this->nullableStr($s, self::X_HELP_TEXT),
            defaultValue: $this->nullableStr($s, 'default'),
        );
    }

    /** @param array<string, mixed> $s */
    private function toObject(string $name, array $s, bool $required): ObjectField
    {
        return new ObjectField($name, $this->str($s, self::X_LABEL, $name), $required, $this->parseObjectFields($s), $this->nullableStr($s, self::X_HELP_TEXT));
    }

    /**
     * @param  array<string, mixed>  $s
     * @return list<SchemaField>
     */
    private function itemFields(array $s): array
    {
        /** @var array<string, mixed> $items */
        $items = is_array($s['items'] ?? null) ? $s['items'] : [];

        return $this->parseObjectFields($items);
    }

    /**
     * @param  array<string, mixed>  $s
     * @return list<SchemaField>
     */
    private function parseObjectFields(array $s): array
    {
        /** @var array<string, array<string, mixed>> $props */
        $props = isset($s['properties']) && is_array($s['properties']) ? $s['properties'] : [];
        /** @var list<string> $req */
        $req = isset($s['required']) && is_array($s['required']) ? $s['required'] : [];

        $fields = [];

        foreach ($props as $subName => $subSchema) {
            $fields[] = $this->toField($subName, $subSchema, in_array($subName, $req, true));
        }

        return $fields;
    }

    /** @param array<string, mixed> $s */
    private function str(array $s, string $key, string $default = ''): string
    {
        $v = $s[$key] ?? $default;

        return is_string($v) ? $v : $default;
    }

    /** @param array<string, mixed> $s */
    private function int(array $s, string $key, int $default = 0): int
    {
        $v = $s[$key] ?? $default;

        return is_int($v) ? $v : $default;
    }

    /** @param array<string, mixed> $s */
    private function nullableInt(array $s, string $key): ?int
    {
        $v = $s[$key] ?? null;

        return is_int($v) ? $v : null;
    }

    /** @param array<string, mixed> $s */
    private function nullableFloat(array $s, string $key): ?float
    {
        $v = $s[$key] ?? null;

        return is_float($v) || is_int($v) ? (float) $v : null;
    }

    /** @param array<string, mixed> $s */
    private function nullableStr(array $s, string $key): ?string
    {
        $v = $s[$key] ?? null;

        return is_string($v) ? $v : null;
    }

    /** @param array<string, mixed> $s */
    private function nullableBool(array $s, string $key): ?bool
    {
        $v = $s[$key] ?? null;

        return is_bool($v) ? $v : null;
    }
}
