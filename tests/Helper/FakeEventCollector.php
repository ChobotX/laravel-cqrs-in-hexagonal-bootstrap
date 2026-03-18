<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\EventCollector;

final class FakeEventCollector implements EventCollector
{
    /** @var list<DomainEvent> */
    public array $collected = [];

    public function collect(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->collected[] = $event;
        }
    }

    /** @return list<DomainEvent> */
    public function flush(): array
    {
        $events = $this->collected;
        $this->collected = [];

        return $events;
    }
}
