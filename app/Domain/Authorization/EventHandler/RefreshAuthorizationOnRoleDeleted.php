<?php

declare(strict_types=1);

namespace App\Domain\Authorization\EventHandler;

use App\Contract\Authorization\AuthorizationRefresher;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Authorization\Event\RoleDeleted;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\UserPermissionRepository;

/** @implements DomainEventHandler<RoleDeleted> */
final readonly class RefreshAuthorizationOnRoleDeleted implements DomainEventHandler
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
