<?php

declare(strict_types=1);

namespace App\Contract\Event;

/**
 * Reacts to a specific {@see DomainEvent} after it has been flushed from the collector (async or deferred path).
 *
 * @template TEvent of DomainEvent
 */
interface DomainEventHandler
{
    /**
     * Idempotent side effects only; must not throw for transient infrastructure issues if the pipeline retries.
     *
     * @param  TEvent  $domainEvent
     */
    public function handle(DomainEvent $domainEvent): void;
}
