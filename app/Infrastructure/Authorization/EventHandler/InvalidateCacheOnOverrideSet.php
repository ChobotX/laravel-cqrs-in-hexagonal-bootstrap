<?php

declare(strict_types=1);

namespace App\Infrastructure\Authorization\EventHandler;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Authorization\Event\PermissionOverrideSet;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/** @implements DomainEventHandler<PermissionOverrideSet> */
final readonly class InvalidateCacheOnOverrideSet implements DomainEventHandler
{
    public function __construct(
        private CacheRepository $cacheRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $this->cacheRepository->forget(sprintf('auth:perms:%s:%s', $domainEvent->organizationId, $domainEvent->userId));
    }
}
