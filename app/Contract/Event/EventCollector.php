<?php

declare(strict_types=1);

namespace App\Contract\Event;

interface EventCollector
{
    public function collect(DomainEvent ...$events): void;

    /** @return list<DomainEvent> */
    public function peek(): array;

    /** @return list<DomainEvent> */
    public function flush(): array;
}
