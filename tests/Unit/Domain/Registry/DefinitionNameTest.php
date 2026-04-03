<?php

declare(strict_types=1);

use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\Exception\InvalidDefinitionNameException;

it('can be constructed with a valid name', function (): void {
    $name = new DefinitionName('Employee Directory');

    expect($name->value)->toBe('Employee Directory');
});

it('trims whitespace', function (): void {
    $name = new DefinitionName('  Employee Directory  ');

    expect($name->value)->toBe('Employee Directory');
});

it('rejects an empty string', function (): void {
    new DefinitionName('');
})->throws(InvalidDefinitionNameException::class, 'Value [] is not a valid definition name.');

it('rejects whitespace-only string', function (): void {
    new DefinitionName('   ');
})->throws(InvalidDefinitionNameException::class, 'Value [   ] is not a valid definition name.');

it('accepts a name at max length', function (): void {
    $name = new DefinitionName(str_repeat('a', 255));

    expect($name->value)->toBe(str_repeat('a', 255));
});

it('rejects a name exceeding max length', function (): void {
    new DefinitionName(str_repeat('a', 256));
})->throws(InvalidDefinitionNameException::class);

it('can compare equality with another DefinitionName', function (): void {
    $name1 = new DefinitionName('Employee Directory');
    $name2 = new DefinitionName('Employee Directory');
    $name3 = new DefinitionName('Project Registry');

    expect($name1->equals($name2))->toBeTrue()
        ->and($name1->equals($name3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $name = new DefinitionName('Employee Directory');

    expect((string) $name)->toBe('Employee Directory');
});
