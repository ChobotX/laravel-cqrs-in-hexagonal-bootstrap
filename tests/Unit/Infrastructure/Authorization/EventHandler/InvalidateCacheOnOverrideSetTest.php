<?php

declare(strict_types=1);

use App\Domain\Authorization\Event\PermissionOverrideSet;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnOverrideSet;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;

it('invalidates cache on override set', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $cache->put('auth:perms:user-1', ['users.list.read' => true], 300);

    $handler = new InvalidateCacheOnOverrideSet($cache);
    $handler->handle(new PermissionOverrideSet('user-1', 'users.list.read', 'grant', new DateTimeImmutable));

    expect($cache->has('auth:perms:user-1'))->toBeFalse();
});
