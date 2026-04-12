<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Infrastructure\Bus\InMemoryEventCollector;

it('starts with no events', function (): void {
    $collector = new InMemoryEventCollector;

    expect($collector->flush())->toBe([])
        ->and($collector->peek())->toBe([]);
});

it('collects events', function (): void {
    $collector = new InMemoryEventCollector;
    $domainEvent = makeStubEvent();
    $event2 = makeStubEvent();

    $collector->collect($domainEvent, $event2);

    expect($collector->flush())->toBe([$domainEvent, $event2]);
});

it('clears events after flush', function (): void {
    $collector = new InMemoryEventCollector;
    $domainEvent = makeStubEvent();

    $collector->collect($domainEvent);
    $collector->flush();

    expect($collector->flush())->toBe([]);
});

it('accumulates events across multiple collect calls', function (): void {
    $collector = new InMemoryEventCollector;
    $domainEvent = makeStubEvent();
    $event2 = makeStubEvent();

    $collector->collect($domainEvent);
    $collector->collect($event2);

    expect($collector->flush())->toBe([$domainEvent, $event2]);
});

it('peek returns collected events without clearing', function (): void {
    $collector = new InMemoryEventCollector;
    $domainEvent = makeStubEvent();

    $collector->collect($domainEvent);

    expect($collector->peek())->toBe([$domainEvent])
        ->and($collector->peek())->toBe([$domainEvent])
        ->and($collector->flush())->toBe([$domainEvent])
        ->and($collector->peek())->toBe([]);
});

function makeStubEvent(): DomainEvent
{
    return new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }

        public function entityType(): string
        {
            return 'stub';
        }

        public function entityId(): string
        {
            return 'stub-id';
        }

        public function actionLabel(): string
        {
            return 'Stub';
        }
    };
}
