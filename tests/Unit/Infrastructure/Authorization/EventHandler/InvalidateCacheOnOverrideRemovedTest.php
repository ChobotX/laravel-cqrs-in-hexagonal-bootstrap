<?php

declare(strict_types=1);

use App\Domain\Authorization\Event\PermissionOverrideRemoved;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnOverrideRemoved;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;

it('invalidates cache on override removed', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $cache->put('auth:perms:user-1', ['users.list.read' => true], 300);

    $handler = new InvalidateCacheOnOverrideRemoved($cache);
    $handler->handle(new PermissionOverrideRemoved('user-1', 'users.list.read', new DateTimeImmutable));

    expect($cache->has('auth:perms:user-1'))->toBeFalse();
});
