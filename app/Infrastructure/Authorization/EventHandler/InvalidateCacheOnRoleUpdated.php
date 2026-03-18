<?php

declare(strict_types=1);

namespace App\Infrastructure\Authorization\EventHandler;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Authorization\Event\RoleUpdated;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/** @implements DomainEventHandler<RoleUpdated> */
final readonly class InvalidateCacheOnRoleUpdated implements DomainEventHandler
{
    public function __construct(
        private CacheRepository $cacheRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $assignments = UserRoleModel::where('role_id', $domainEvent->roleId)
            ->get(['user_id', 'organization_id']);

        foreach ($assignments as $assignment) {
            $this->cacheRepository->forget(sprintf('auth:perms:%s:%s', $assignment->organization_id, $assignment->user_id));
        }
    }
}
