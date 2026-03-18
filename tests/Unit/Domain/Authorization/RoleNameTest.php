<?php

declare(strict_types=1);

use App\Domain\Authorization\Exception\InvalidRoleNameException;
use App\Domain\Authorization\RoleName;

it('creates a valid role name', function (): void {
    $name = new RoleName('Admin');

    expect($name->value)->toBe('Admin')
        ->and((string) $name)->toBe('Admin');
});

it('trims whitespace', function (): void {
    $name = new RoleName('  Editor  ');

    expect($name->value)->toBe('Editor');
});

it('throws on empty string', function (): void {
    new RoleName('');
})->throws(InvalidRoleNameException::class);

it('throws on whitespace-only string', function (): void {
    new RoleName('   ');
})->throws(InvalidRoleNameException::class);

it('compares equality', function (): void {
    $a = new RoleName('Admin');
    $b = new RoleName('Admin');
    $c = new RoleName('Editor');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
