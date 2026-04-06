<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Exception\InvalidFlagValueException;
use App\Domain\FeatureFlag\Service\FlagValueValidator;

it('accepts valid boolean true', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Boolean,
        default: '0',
        defaultEnabled: false,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
    );

    $validator->validate($definition, '1');

    expect(true)->toBeTrue();
});

it('accepts valid boolean false', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Boolean,
        default: '0',
        defaultEnabled: false,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
    );

    $validator->validate($definition, '0');

    expect(true)->toBeTrue();
});

it('rejects invalid boolean value', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Boolean,
        default: '0',
        defaultEnabled: false,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
    );

    $validator->validate($definition, 'true');
})->throws(InvalidFlagValueException::class, 'Boolean flags accept only "0" or "1".');

it('accepts valid select option', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Select,
        default: 'a',
        defaultEnabled: true,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
        options: ['a', 'b', 'c'],
    );

    $validator->validate($definition, 'b');

    expect(true)->toBeTrue();
});

it('rejects invalid select option', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Select,
        default: 'a',
        defaultEnabled: true,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
        options: ['a', 'b', 'c'],
    );

    $validator->validate($definition, 'd');
})->throws(InvalidFlagValueException::class, 'Value must be one of: a, b, c.');

it('rejects select with null options', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Select,
        default: 'a',
        defaultEnabled: true,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
    );

    $validator->validate($definition, 'a');
})->throws(InvalidFlagValueException::class);

it('accepts input matching pattern', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Input,
        default: 'INV-',
        defaultEnabled: false,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
        pattern: '/^[A-Z]{2,5}-$/',
    );

    $validator->validate($definition, 'FAK-');

    expect(true)->toBeTrue();
});

it('rejects input not matching pattern', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Input,
        default: 'INV-',
        defaultEnabled: false,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
        pattern: '/^[A-Z]{2,5}-$/',
    );

    $validator->validate($definition, 'invalid');
})->throws(InvalidFlagValueException::class);

it('accepts any input when no pattern', function (): void {
    $validator = new FlagValueValidator;

    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Input,
        default: 'default',
        defaultEnabled: true,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
    );

    $validator->validate($definition, 'anything');

    expect(true)->toBeTrue();
});
