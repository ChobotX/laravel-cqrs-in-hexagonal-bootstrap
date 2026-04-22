<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Attribute\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;

/** @implements DomainEventHandler<DomainEvent> */
#[RetryPolicy(tries: 1, backoff: [], timeout: 10)]
final class FakeDomainEventHandler implements DomainEventHandler
{
    public function handle(DomainEvent $domainEvent): void {}
}
