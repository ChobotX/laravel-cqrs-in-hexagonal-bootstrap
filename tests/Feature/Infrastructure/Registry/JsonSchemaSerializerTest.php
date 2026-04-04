<?php

declare(strict_types=1);

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
use App\Domain\Registry\Schema\StringField;
use App\Infrastructure\Registry\JsonSchemaDeserializer;
use App\Infrastructure\Registry\JsonSchemaSerializerImpl;

function deserializer(): JsonSchemaDeserializer
{
    return new JsonSchemaDeserializer;
}

function serializer(): JsonSchemaSerializerImpl
{
    return new JsonSchemaSerializerImpl(deserializer());
}

it('serializes and deserializes a StringField', function (): void {
    $schema = new Schema([new StringField('name', 'Name', true, false, 1, 255)]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields)->toHaveCount(1)
        ->and($result->fields[0])->toBeInstanceOf(StringField::class)
        ->and($result->fields[0]->name())->toBe('name')
        ->and($result->fields[0]->isRequired())->toBeTrue();
});

it('serializes and deserializes a multiline StringField', function (): void {
    $schema = new Schema([new StringField('bio', 'Bio', false, true)]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(StringField::class);
    assert($result->fields[0] instanceof StringField);
    expect($result->fields[0]->multiline)->toBeTrue();
});

it('serializes and deserializes an IntegerField', function (): void {
    $schema = new Schema([new IntegerField('age', 'Age', true, 0, 150)]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(IntegerField::class);
    assert($result->fields[0] instanceof IntegerField);
    expect($result->fields[0]->min)->toBe(0)
        ->and($result->fields[0]->max)->toBe(150);
});

it('serializes and deserializes a NumberField', function (): void {
    $schema = new Schema([new NumberField('price', 'Price', false, 0.01, 99999.99)]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(NumberField::class);
});

it('serializes and deserializes a BooleanField', function (): void {
    $schema = new Schema([new BooleanField('active', 'Active')]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(BooleanField::class);
});

it('serializes and deserializes a DateField', function (): void {
    $schema = new Schema([new DateField('dob', 'Date of Birth', true)]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(DateField::class);
});

it('serializes and deserializes an EmailField', function (): void {
    $schema = new Schema([new EmailField('email', 'Email', true)]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(EmailField::class);
});

it('serializes and deserializes a ReferenceField', function (): void {
    $schema = new Schema([new ReferenceField('brand', 'Brand', true, 'enumerations', 'car_brand')]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(ReferenceField::class);
    assert($result->fields[0] instanceof ReferenceField);
    expect($result->fields[0]->referenceNamespace)->toBe('enumerations')
        ->and($result->fields[0]->referenceSlug)->toBe('car_brand');
});

it('serializes and deserializes a FileField', function (): void {
    $schema = new Schema([new FileField('photo', 'Photo', true, ['image/jpeg', 'image/png'], 5242880, 'avatars')]);
    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(FileField::class);
    assert($result->fields[0] instanceof FileField);
    expect($result->fields[0]->allowedMimeTypes)->toBe(['image/jpeg', 'image/png'])
        ->and($result->fields[0]->maxSizeBytes)->toBe(5242880)
        ->and($result->fields[0]->fileNamespace)->toBe('avatars');
});

it('serializes and deserializes a RepeaterField', function (): void {
    $schema = new Schema([new RepeaterField('items', 'Items', true, [
        new StringField('key', 'Key', true),
        new StringField('value', 'Value', true),
    ], 1, 10)]);

    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(RepeaterField::class);
    assert($result->fields[0] instanceof RepeaterField);
    expect($result->fields[0]->fields)->toHaveCount(2)
        ->and($result->fields[0]->minItems)->toBe(1)
        ->and($result->fields[0]->maxItems)->toBe(10);
});

it('serializes and deserializes an ObjectField', function (): void {
    $schema = new Schema([new ObjectField('address', 'Address', true, [
        new StringField('street', 'Street', true),
        new StringField('city', 'City', true),
    ])]);

    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields[0])->toBeInstanceOf(ObjectField::class);
    assert($result->fields[0] instanceof ObjectField);
    expect($result->fields[0]->properties)->toHaveCount(2);
});

it('deserializes a raw array type as repeater', function (): void {
    $json = [
        'type' => 'object',
        'properties' => [
            'tags' => [
                'type' => 'array',
                'x-field-label' => 'Tags',
                'minItems' => 1,
                'maxItems' => 5,
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string', 'x-field-label' => 'Name'],
                    ],
                    'required' => ['name'],
                ],
            ],
        ],
        'required' => ['tags'],
    ];

    $schema = serializer()->fromJsonSchema($json);

    expect($schema->fields[0])->toBeInstanceOf(RepeaterField::class);
    assert($schema->fields[0] instanceof RepeaterField);
    expect($schema->fields[0]->minItems)->toBe(1)
        ->and($schema->fields[0]->maxItems)->toBe(5)
        ->and($schema->fields[0]->fields)->toHaveCount(1);
});

it('serializes a schema with all field types', function (): void {
    $schema = new Schema([
        new StringField('name', 'Name', true),
        new IntegerField('age', 'Age'),
        new NumberField('salary', 'Salary'),
        new BooleanField('active', 'Active'),
        new DateField('dob', 'DOB'),
        new EmailField('email', 'Email'),
        new ReferenceField('team', 'Team', false, 'core', 'teams'),
        new FileField('avatar', 'Avatar'),
        new RepeaterField('tags', 'Tags', false, [new StringField('tag', 'Tag')]),
        new ObjectField('meta', 'Meta', false, [new StringField('key', 'Key')]),
    ]);

    $json = serializer()->toJsonSchema($schema);
    $result = serializer()->fromJsonSchema($json);

    expect($result->fields)->toHaveCount(10);
});
