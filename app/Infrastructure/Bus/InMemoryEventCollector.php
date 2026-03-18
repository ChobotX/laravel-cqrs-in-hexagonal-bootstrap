<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\EventCollector;

final class InMemoryEventCollector implements EventCollector
{
    /** @var list<DomainEvent> */
    private array $events = [];

    public function collect(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->events[] = $event;
        }
    }

    /** @return list<DomainEvent> */
    public function flush(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
