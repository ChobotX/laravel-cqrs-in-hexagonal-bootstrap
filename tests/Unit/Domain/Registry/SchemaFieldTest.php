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
    $field = new StringField('bio', 'Bio', true, true, 10, 500, '^[A-Z]', 'Enter bio', 'Short biography', 'Default bio');

    expect($field->name())->toBe('bio')
        ->and($field->label())->toBe('Bio')
        ->and($field->type())->toBe(FieldType::String)
        ->and($field->isRequired())->toBeTrue()
        ->and($field->multiline)->toBeTrue()
        ->and($field->minLength)->toBe(10)
        ->and($field->maxLength)->toBe(500)
        ->and($field->pattern)->toBe('^[A-Z]')
        ->and($field->placeholder)->toBe('Enter bio')
        ->and($field->helpText)->toBe('Short biography')
        ->and($field->defaultValue)->toBe('Default bio');
});

it('creates an IntegerField with all options', function (): void {
    $field = new IntegerField('age', 'Age', true, 0, 150, 1, 'Enter age', 'Years', 25);

    expect($field->min)->toBe(0)
        ->and($field->max)->toBe(150)
        ->and($field->step)->toBe(1)
        ->and($field->placeholder)->toBe('Enter age')
        ->and($field->helpText)->toBe('Years')
        ->and($field->defaultValue)->toBe(25);
});

it('creates a NumberField with all options', function (): void {
    $field = new NumberField('price', 'Price', false, 0.01, 9999.99, 0.01, 'Enter price', 'In CZK', 100.0);

    expect($field->step)->toBe(0.01)
        ->and($field->placeholder)->toBe('Enter price')
        ->and($field->helpText)->toBe('In CZK')
        ->and($field->defaultValue)->toBe(100.0);
});

it('creates a DateField with all options', function (): void {
    $field = new DateField('born', 'Born', false, '2000-01-01', '2025-12-31', 'Select date', 'Date of birth', '2000-06-15');

    expect($field->minDate)->toBe('2000-01-01')
        ->and($field->maxDate)->toBe('2025-12-31')
        ->and($field->placeholder)->toBe('Select date')
        ->and($field->helpText)->toBe('Date of birth')
        ->and($field->defaultValue)->toBe('2000-06-15');
});

it('creates a BooleanField with all options', function (): void {
    $field = new BooleanField('active', 'Active', false, 'Toggle status', true);

    expect($field->helpText)->toBe('Toggle status')
        ->and($field->defaultValue)->toBeTrue();
});

it('creates an EmailField with all options', function (): void {
    $field = new EmailField('email', 'Email', true, 'user@example.com', 'Work email');

    expect($field->placeholder)->toBe('user@example.com')
        ->and($field->helpText)->toBe('Work email');
});

it('creates a FileField with helpText', function (): void {
    $field = new FileField('doc', 'Document', false, null, null, null, 'Upload a PDF');

    expect($field->helpText)->toBe('Upload a PDF');
});

it('creates a ReferenceField with all options', function (): void {
    $field = new ReferenceField('brand', 'Brand', true, 'enumerations', 'car_brand', 'Select brand', 'Pick one');

    expect($field->placeholder)->toBe('Select brand')
        ->and($field->helpText)->toBe('Pick one');
});

it('creates a RepeaterField with helpText', function (): void {
    $inner = new StringField('key', 'Key', true);
    $field = new RepeaterField('meta', 'Meta', true, [$inner], 0, null, 'Key-value pairs');

    expect($field->helpText)->toBe('Key-value pairs');
});

it('creates an ObjectField with helpText', function (): void {
    $inner = new StringField('street', 'Street');
    $field = new ObjectField('address', 'Address', false, [$inner], 'Full address');

    expect($field->helpText)->toBe('Full address');
});
