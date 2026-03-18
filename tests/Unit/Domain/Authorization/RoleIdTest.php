<?php

declare(strict_types=1);

use App\Domain\Authorization\Exception\InvalidRoleIdException;
use App\Domain\Authorization\RoleId;

it('creates a valid role id', function (): void {
    $id = new RoleId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws on invalid uuid', function (): void {
    new RoleId('not-a-uuid');
})->throws(InvalidRoleIdException::class);

it('throws on empty string', function (): void {
    new RoleId('');
})->throws(InvalidRoleIdException::class);

it('compares equality', function (): void {
    $a = new RoleId('550e8400-e29b-41d4-a716-446655440000');
    $b = new RoleId('550e8400-e29b-41d4-a716-446655440000');
    $c = new RoleId('660e8400-e29b-41d4-a716-446655440000');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
