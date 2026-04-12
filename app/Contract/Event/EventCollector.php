<?php

declare(strict_types=1);

namespace App\Contract\Event;

/**
 * Buffers domain events raised during a single command so middleware can persist and dispatch them atomically.
 */
interface EventCollector
{
    /** Queues events without clearing existing buffered events. */
    public function collect(DomainEvent ...$events): void;

    /**
     * Returns the current buffer without removing events (for inspection or duplicate checks).
     *
     * @return list<DomainEvent>
     */
    public function peek(): array;

    /**
     * Returns all buffered events and clears the buffer. Call once per successful command boundary.
     *
     * @return list<DomainEvent>
     */
    public function flush(): array;
}
