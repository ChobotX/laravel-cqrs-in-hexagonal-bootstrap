<?php

declare(strict_types=1);

use App\Domain\Authorization\Event\RoleAssignedToUser;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleAssigned;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;

it('invalidates cache on role assigned', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $cache->put('auth:perms:user-1', ['users.list.read' => true], 300);

    $handler = new InvalidateCacheOnRoleAssigned($cache);
    $handler->handle(new RoleAssignedToUser('user-1', 'role-1', new DateTimeImmutable));

    expect($cache->has('auth:perms:user-1'))->toBeFalse();
});

it('does not affect other users cache', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $cache->put('auth:perms:user-1', ['data'], 300);
    $cache->put('auth:perms:user-2', ['data'], 300);

    $handler = new InvalidateCacheOnRoleAssigned($cache);
    $handler->handle(new RoleAssignedToUser('user-1', 'role-1', new DateTimeImmutable));

    expect($cache->has('auth:perms:user-1'))->toBeFalse()
        ->and($cache->has('auth:perms:user-2'))->toBeTrue();
});
