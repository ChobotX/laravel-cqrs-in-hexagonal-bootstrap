<?php

declare(strict_types=1);

use App\Domain\Authorization\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\Event\PermissionOverrideSet;
use App\Domain\Authorization\Event\RoleAssignedToUser;
use App\Domain\Authorization\Event\RoleDeleted;
use App\Domain\Authorization\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Event\RoleUpdated;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnOverrideRemoved;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnOverrideSet;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleAssigned;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleDeleted;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleRevoked;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleUpdated;
use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Str;

it('invalidates cache on role assigned', function (): void {
    $repository = app(CacheContract::class);
    $repository->put('auth:perms:user-1', ['data'], 300);

    $handler = new InvalidateCacheOnRoleAssigned($repository);
    $handler->handle(new RoleAssignedToUser('user-1', 'role-1', new DateTimeImmutable));

    expect($repository->has('auth:perms:user-1'))->toBeFalse();
});

it('invalidates cache on role revoked', function (): void {
    $repository = app(CacheContract::class);
    $repository->put('auth:perms:user-1', ['data'], 300);

    $handler = new InvalidateCacheOnRoleRevoked($repository);
    $handler->handle(new RoleRevokedFromUser('user-1', 'role-1', new DateTimeImmutable));

    expect($repository->has('auth:perms:user-1'))->toBeFalse();
});

it('invalidates cache on override set', function (): void {
    $repository = app(CacheContract::class);
    $repository->put('auth:perms:user-1', ['data'], 300);

    $handler = new InvalidateCacheOnOverrideSet($repository);
    $handler->handle(new PermissionOverrideSet('user-1', 'users.list.read', 'deny', new DateTimeImmutable));

    expect($repository->has('auth:perms:user-1'))->toBeFalse();
});

it('invalidates cache on override removed', function (): void {
    $repository = app(CacheContract::class);
    $repository->put('auth:perms:user-1', ['data'], 300);

    $handler = new InvalidateCacheOnOverrideRemoved($repository);
    $handler->handle(new PermissionOverrideRemoved('user-1', 'users.list.read', new DateTimeImmutable));

    expect($repository->has('auth:perms:user-1'))->toBeFalse();
});

it('invalidates cache for all users on role updated', function (): void {
    $repository = app(CacheContract::class);
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440500', 'name' => 'U1', 'email' => 'u1@evt.com']);
    $role = RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440501', 'name' => 'R', 'description' => 'R', 'is_system' => false]);
    UserRoleModel::create(['id' => Str::uuid()->toString(), 'user_id' => $user->id, 'role_id' => $role->id]);

    $repository->put('auth:perms:'.$user->id, ['data'], 300);

    $handler = new InvalidateCacheOnRoleUpdated($repository);
    $handler->handle(new RoleUpdated($role->id, 'R', new DateTimeImmutable));

    expect($repository->has('auth:perms:'.$user->id))->toBeFalse();
});

it('invalidates cache for all users on role deleted', function (): void {
    $repository = app(CacheContract::class);
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440502', 'name' => 'U2', 'email' => 'u2@evt.com']);
    $role = RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440503', 'name' => 'R2', 'description' => 'R2', 'is_system' => false]);
    UserRoleModel::create(['id' => Str::uuid()->toString(), 'user_id' => $user->id, 'role_id' => $role->id]);

    $repository->put('auth:perms:'.$user->id, ['data'], 300);

    $handler = new InvalidateCacheOnRoleDeleted($repository);
    $handler->handle(new RoleDeleted($role->id, new DateTimeImmutable));

    expect($repository->has('auth:perms:'.$user->id))->toBeFalse();
});
