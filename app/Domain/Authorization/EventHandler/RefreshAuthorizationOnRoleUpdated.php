<?php

declare(strict_types=1);

namespace App\Domain\Authorization\EventHandler;

use App\Application\Event\RetryPolicy;
use App\Contract\Authorization\AuthorizationRefresher;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Authorization\Event\RoleUpdated;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\UserPermissionRepository;

/** @implements DomainEventHandler<RoleUpdated> */
#[RetryPolicy(tries: 3, backoff: [5, 15, 30], timeout: 10)]
final readonly class RefreshAuthorizationOnRoleUpdated implements DomainEventHandler
{
    public function __construct(
        private AuthorizationRefresher $authorizationRefresher,
        private UserPermissionRepository $userPermissionRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $userIds = $this->userPermissionRepository->userIdsWithRole(
            new RoleId($domainEvent->roleId),
        );

        foreach ($userIds as $userId) {
            $this->authorizationRefresher->refreshForUser($userId);
        }
    }
}
