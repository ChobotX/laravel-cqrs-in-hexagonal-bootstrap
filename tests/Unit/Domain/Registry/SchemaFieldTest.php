<?php

declare(strict_types=1);

use App\Domain\Registry\Schema\BooleanField;
use App\Domain\Registry\Schema\DateField;
use App\Domain\Registry\Schema\EmailField;
use App\Domain\Registry\Schema\FieldType;
use App\Domain\Registry\Schema\FileField;
use App\Domain\Registry\Schema\IntegerField;
use App\Domain\Registry\Schema\NumberField;
use App\Domain\Registry\Schema\ObjectField;
use App\Domain\Registry\Schema\ReferenceField;
use App\Domain\Registry\Schema\RepeaterField;
use App\Domain\Registry\Schema\StringField;

it('creates a BooleanField', function (): void {
    $field = new BooleanField('active', 'Active', true);

    expect($field->name())->toBe('active')
        ->and($field->label())->toBe('Active')
        ->and($field->type())->toBe(FieldType::Boolean)
        ->and($field->isRequired())->toBeTrue();
});

it('creates a DateField', function (): void {
    $field = new DateField('born', 'Born');

    expect($field->name())->toBe('born')
        ->and($field->label())->toBe('Born')
        ->and($field->type())->toBe(FieldType::Date)
        ->and($field->isRequired())->toBeFalse();
});

it('creates an EmailField', function (): void {
    $field = new EmailField('email', 'Email', true);

    expect($field->name())->toBe('email')
        ->and($field->label())->toBe('Email')
        ->and($field->type())->toBe(FieldType::Email)
        ->and($field->isRequired())->toBeTrue();
});

it('creates a FileField', function (): void {
    $field = new FileField('photo', 'Photo', true, ['image/jpeg'], 5242880, 'avatars');

    expect($field->name())->toBe('photo')
        ->and($field->label())->toBe('Photo')
        ->and($field->type())->toBe(FieldType::File)
        ->and($field->isRequired())->toBeTrue()
        ->and($field->allowedMimeTypes)->toBe(['image/jpeg'])
        ->and($field->maxSizeBytes)->toBe(5242880)
        ->and($field->fileNamespace)->toBe('avatars');
});

it('creates an IntegerField', function (): void {
    $field = new IntegerField('age', 'Age', true, 0, 150);

    expect($field->name())->toBe('age')
        ->and($field->label())->toBe('Age')
        ->and($field->type())->toBe(FieldType::Integer)
        ->and($field->isRequired())->toBeTrue()
        ->and($field->min)->toBe(0)
        ->and($field->max)->toBe(150);
});

it('creates a NumberField', function (): void {
    $field = new NumberField('price', 'Price', false, 0.01);

    expect($field->name())->toBe('price')
        ->and($field->label())->toBe('Price')
        ->and($field->type())->toBe(FieldType::Number)
        ->and($field->isRequired())->toBeFalse()
        ->and($field->min)->toBe(0.01);
});

it('creates an ObjectField', function (): void {
    $inner = new StringField('street', 'Street');
    $field = new ObjectField('address', 'Address', true, [$inner]);

    expect($field->name())->toBe('address')
        ->and($field->label())->toBe('Address')
        ->and($field->type())->toBe(FieldType::Object)
        ->and($field->isRequired())->toBeTrue()
        ->and($field->properties)->toHaveCount(1);
});

it('creates a ReferenceField', function (): void {
    $field = new ReferenceField('brand', 'Brand', true, 'enumerations', 'car_brand');

    expect($field->name())->toBe('brand')
        ->and($field->label())->toBe('Brand')
        ->and($field->type())->toBe(FieldType::Reference)
        ->and($field->isRequired())->toBeTrue()
        ->and($field->referenceNamespace)->toBe('enumerations')
        ->and($field->referenceSlug)->toBe('car_brand');
});

it('creates a RepeaterField', function (): void {
    $inner = new StringField('key', 'Key', true);
    $field = new RepeaterField('metadata', 'Metadata', true, [$inner], 1, 10);

    expect($field->name())->toBe('metadata')
        ->and($field->label())->toBe('Metadata')
        ->and($field->type())->toBe(FieldType::Repeater)
        ->and($field->isRequired())->toBeTrue()
        ->and($field->fields)->toHaveCount(1)
        ->and($field->minItems)->toBe(1)
        ->and($field->maxItems)->toBe(10);
});

it('creates a StringField with all options', function (): void {
    $field = new StringField('bio', 'Bio', true, true, 10, 500);

    expect($field->name())->toBe('bio')
        ->and($field->label())->toBe('Bio')
        ->and($field->type())->toBe(FieldType::String)
        ->and($field->isRequired())->toBeTrue()
        ->and($field->multiline)->toBeTrue()
        ->and($field->minLength)->toBe(10)
        ->and($field->maxLength)->toBe(500);
});
