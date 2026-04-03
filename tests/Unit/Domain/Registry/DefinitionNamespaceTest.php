<?php

declare(strict_types=1);

use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\Exception\InvalidDefinitionNamespaceException;

it('can be constructed with a valid namespace', function (string $value): void {
    $namespace = new DefinitionNamespace($value);

    expect($namespace->value)->toBe($value);
})->with(['users', 'teams', 'projects', 'a', 'abc_def', 'a123']);

it('rejects a namespace starting with a number', function (): void {
    new DefinitionNamespace('1users');
})->throws(InvalidDefinitionNamespaceException::class, 'Value [1users] is not a valid definition namespace.');

it('rejects uppercase characters', function (): void {
    new DefinitionNamespace('Users');
})->throws(InvalidDefinitionNamespaceException::class, 'Value [Users] is not a valid definition namespace.');

it('rejects spaces', function (): void {
    new DefinitionNamespace('my users');
})->throws(InvalidDefinitionNamespaceException::class, 'Value [my users] is not a valid definition namespace.');

it('rejects an empty string', function (): void {
    new DefinitionNamespace('');
})->throws(InvalidDefinitionNamespaceException::class, 'Value [] is not a valid definition namespace.');

it('rejects hyphens', function (): void {
    new DefinitionNamespace('my-namespace');
})->throws(InvalidDefinitionNamespaceException::class, 'Value [my-namespace] is not a valid definition namespace.');

it('can compare equality with another DefinitionNamespace', function (): void {
    $ns1 = new DefinitionNamespace('users');
    $ns2 = new DefinitionNamespace('users');
    $ns3 = new DefinitionNamespace('teams');

    expect($ns1->equals($ns2))->toBeTrue()
        ->and($ns1->equals($ns3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $namespace = new DefinitionNamespace('users');

    expect((string) $namespace)->toBe('users');
});
