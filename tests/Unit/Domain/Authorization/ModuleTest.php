<?php

declare(strict_types=1);

use App\Domain\Authorization\Exception\InvalidPermissionKeyException;
use App\Domain\Authorization\ValueObject\Module;

it('creates a valid module', function (): void {
    $module = new Module('users');

    expect($module->value)->toBe('users')
        ->and((string) $module)->toBe('users');
});

it('allows underscores and digits', function (): void {
    $module = new Module('crm_v2');

    expect($module->value)->toBe('crm_v2');
});

it('throws on invalid slug with uppercase', function (): void {
    new Module('Users');
})->throws(InvalidPermissionKeyException::class);

it('throws on empty string', function (): void {
    new Module('');
})->throws(InvalidPermissionKeyException::class);

it('throws on slug starting with digit', function (): void {
    new Module('2crm');
})->throws(InvalidPermissionKeyException::class);

it('throws on slug with special characters', function (): void {
    new Module('crm-v2');
})->throws(InvalidPermissionKeyException::class);

it('compares equality', function (): void {
    $a = new Module('users');
    $b = new Module('users');
    $c = new Module('crm');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
