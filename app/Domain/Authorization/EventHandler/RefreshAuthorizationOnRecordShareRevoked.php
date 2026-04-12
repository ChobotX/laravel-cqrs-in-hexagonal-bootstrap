<?php

declare(strict_types=1);

namespace App\Domain\Authorization\EventHandler;

use App\Application\Event\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Authorization\Contract\Event\RecordShareRevoked;
use App\Domain\Authorization\Contract\Service\AuthorizationRefresher;

/** @implements DomainEventHandler<RecordShareRevoked> */
#[RetryPolicy(tries: 3, backoff: [5, 15, 30], timeout: 10)]
final readonly class RefreshAuthorizationOnRecordShareRevoked implements DomainEventHandler
{
    public function __construct(
        private AuthorizationRefresher $authorizationRefresher,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $this->authorizationRefresher->refreshForUser($domainEvent->granteeUserId);
    }
}
