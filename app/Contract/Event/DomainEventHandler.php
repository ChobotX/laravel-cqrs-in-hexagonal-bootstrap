<?php

declare(strict_types=1);

namespace App\Contract\Event;

/**
 * @template TEvent of DomainEvent
 */
interface DomainEventHandler
{
    /**
     * @param  TEvent  $domainEvent
     */
    public function handle(DomainEvent $domainEvent): void;
}
