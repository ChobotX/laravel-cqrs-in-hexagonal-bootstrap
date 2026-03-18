<?php

declare(strict_types=1);

use App\Domain\Authorization\Action;
use App\Domain\Authorization\Exception\InvalidPermissionKeyException;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;

it('creates a module-only key', function (): void {
    $key = new PermissionKey(new Module('users'));

    expect((string) $key)->toBe('users')
        ->and($key->module->value)->toBe('users')
        ->and($key->feature)->toBeNull()
        ->and($key->action)->toBeNull()
        ->and($key->isLeaf())->toBeFalse();
});

it('creates a module.feature key', function (): void {
    $key = new PermissionKey(new Module('users'), new Feature('list'));

    expect((string) $key)->toBe('users.list')
        ->and($key->isLeaf())->toBeFalse();
});

it('creates a module.feature.action key', function (): void {
    $key = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);

    expect((string) $key)->toBe('users.list.read')
        ->and($key->isLeaf())->toBeTrue();
});

it('throws when action is set without feature', function (): void {
    new PermissionKey(new Module('users'), null, Action::Read);
})->throws(InvalidPermissionKeyException::class);

it('covers: module covers all features and actions', function (): void {
    $parent = new PermissionKey(new Module('users'));
    $child = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);

    expect($parent->covers($child))->toBeTrue();
});

it('covers: feature covers all actions', function (): void {
    $parent = new PermissionKey(new Module('users'), new Feature('list'));
    $child = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);

    expect($parent->covers($child))->toBeTrue();
});

it('covers: action covers only same action', function (): void {
    $same = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);
    $other = new PermissionKey(new Module('users'), new Feature('list'), Action::Create);

    expect($same->covers($same))->toBeTrue()
        ->and($same->covers($other))->toBeFalse();
});

it('covers: different modules do not cover each other', function (): void {
    $users = new PermissionKey(new Module('users'));
    $crm = new PermissionKey(new Module('crm'), new Feature('contacts'), Action::Read);

    expect($users->covers($crm))->toBeFalse();
});

it('covers: feature key does not cover module-only key', function (): void {
    $parent = new PermissionKey(new Module('users'), new Feature('list'));
    $child = new PermissionKey(new Module('users'));

    expect($parent->covers($child))->toBeFalse();
});

it('covers: feature key does not cover different feature', function (): void {
    $parent = new PermissionKey(new Module('users'), new Feature('list'));
    $child = new PermissionKey(new Module('users'), new Feature('profile'), Action::Read);

    expect($parent->covers($child))->toBeFalse();
});

it('covers: action key does not cover feature-only key', function (): void {
    $parent = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);
    $child = new PermissionKey(new Module('users'), new Feature('list'));

    expect($parent->covers($child))->toBeFalse();
});

it('equals compares string representation', function (): void {
    $a = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);
    $b = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);
    $c = new PermissionKey(new Module('users'), new Feature('list'), Action::Create);

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
