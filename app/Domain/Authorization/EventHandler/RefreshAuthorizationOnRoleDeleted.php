<?php

declare(strict_types=1);

namespace App\Domain\Authorization\EventHandler;

use App\Contract\Attribute\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Authorization\Contract\Event\RoleDeleted;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\Service\AuthorizationRefresher;
use App\Domain\Authorization\Contract\ValueObject\RoleId;

/** @implements DomainEventHandler<RoleDeleted> */
#[RetryPolicy(tries: 3, backoff: [5, 15, 30], timeout: 10)]
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
