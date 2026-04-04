<?php

declare(strict_types=1);

use App\Domain\Authorization\Enum\Feature;
use App\Domain\Authorization\Exception\InvalidPermissionKeyException;

it('creates a valid feature', function (): void {
    $feature = new Feature('list');

    expect($feature->value)->toBe('list')
        ->and((string) $feature)->toBe('list');
});

it('throws on invalid slug', function (): void {
    new Feature('My Feature');
})->throws(InvalidPermissionKeyException::class);

it('throws on empty string', function (): void {
    new Feature('');
})->throws(InvalidPermissionKeyException::class);

it('compares equality', function (): void {
    $a = new Feature('list');
    $b = new Feature('list');
    $c = new Feature('contacts');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
