<?php

declare(strict_types=1);

namespace App\Domain\Authorization\EventHandler;

use App\Contract\Authorization\AuthorizationRefresher;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Authorization\Event\RoleAssignedToUser;

/** @implements DomainEventHandler<RoleAssignedToUser> */
final readonly class RefreshAuthorizationOnRoleAssigned implements DomainEventHandler
{
    public function __construct(
        private AuthorizationRefresher $authorizationRefresher,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $this->authorizationRefresher->refreshForUser($domainEvent->userId);
    }
}
