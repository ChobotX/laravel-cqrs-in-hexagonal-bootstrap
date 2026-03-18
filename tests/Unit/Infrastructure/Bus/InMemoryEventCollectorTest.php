<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Infrastructure\Bus\InMemoryEventCollector;

it('starts with no events', function (): void {
    $collector = new InMemoryEventCollector;

    expect($collector->flush())->toBe([]);
});

it('collects events', function (): void {
    $collector = new InMemoryEventCollector;
    $event1 = new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };
    $event2 = new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $collector->collect($event1, $event2);

    expect($collector->flush())->toBe([$event1, $event2]);
});

it('clears events after flush', function (): void {
    $collector = new InMemoryEventCollector;
    $event = new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $collector->collect($event);
    $collector->flush();

    expect($collector->flush())->toBe([]);
});

it('accumulates events across multiple collect calls', function (): void {
    $collector = new InMemoryEventCollector;
    $event1 = new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };
    $event2 = new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $collector->collect($event1);
    $collector->collect($event2);

    expect($collector->flush())->toBe([$event1, $event2]);
});
