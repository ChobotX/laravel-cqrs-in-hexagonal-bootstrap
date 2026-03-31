<?php

declare(strict_types=1);

use App\Domain\Label\Exception\InvalidLabelNamespaceException;
use App\Domain\Label\LabelNamespace;

it('can be constructed with a valid namespace', function (string $value): void {
    $namespace = new LabelNamespace($value);

    expect($namespace->value)->toBe($value);
})->with(['users', 'teams', 'projects', 'a', 'abc_def', 'a123']);

it('rejects a namespace starting with a number', function (): void {
    new LabelNamespace('1users');
})->throws(InvalidLabelNamespaceException::class, 'Value [1users] is not a valid label namespace.');

it('rejects uppercase characters', function (): void {
    new LabelNamespace('Users');
})->throws(InvalidLabelNamespaceException::class, 'Value [Users] is not a valid label namespace.');

it('rejects spaces', function (): void {
    new LabelNamespace('my users');
})->throws(InvalidLabelNamespaceException::class, 'Value [my users] is not a valid label namespace.');

it('rejects an empty string', function (): void {
    new LabelNamespace('');
})->throws(InvalidLabelNamespaceException::class, 'Value [] is not a valid label namespace.');

it('rejects hyphens', function (): void {
    new LabelNamespace('my-namespace');
})->throws(InvalidLabelNamespaceException::class, 'Value [my-namespace] is not a valid label namespace.');

it('can compare equality with another LabelNamespace', function (): void {
    $ns1 = new LabelNamespace('users');
    $ns2 = new LabelNamespace('users');
    $ns3 = new LabelNamespace('teams');

    expect($ns1->equals($ns2))->toBeTrue()
        ->and($ns1->equals($ns3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $namespace = new LabelNamespace('users');

    expect((string) $namespace)->toBe('users');
});
