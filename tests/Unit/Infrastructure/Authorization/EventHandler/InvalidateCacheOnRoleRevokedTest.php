<?php

declare(strict_types=1);

use App\Domain\Authorization\Event\RoleRevokedFromUser;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleRevoked;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;

it('invalidates cache on role revoked', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $cache->put('auth:perms:org-1:user-1', ['users.list.read' => true], 300);

    $handler = new InvalidateCacheOnRoleRevoked($cache);
    $handler->handle(new RoleRevokedFromUser('user-1', 'role-1', 'org-1', new DateTimeImmutable));

    expect($cache->has('auth:perms:org-1:user-1'))->toBeFalse();
});
