<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Infrastructure\Bus\QueuedEventBus;
use Tests\Helper\FakeDispatcher;

it('dispatches a job for each handler registered for an event', function (): void {
    $fakeDispatcher = new FakeDispatcher;

    $event = new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $handlerA = new class implements DomainEventHandler
    {
        public function handle(DomainEvent $domainEvent): void {}
    };
    $handlerB = new class implements DomainEventHandler
    {
        public function handle(DomainEvent $domainEvent): void {}
    };

    $bus = new QueuedEventBus(
        dispatcher: $fakeDispatcher,
        handlers: [
            $event::class => [$handlerA::class, $handlerB::class],
        ],
    );

    $bus->dispatch($event);

    expect($fakeDispatcher->dispatched)->toHaveCount(2);
});

it('dispatches no jobs when no handlers are registered', function (): void {
    $fakeDispatcher = new FakeDispatcher;

    $event = new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $bus = new QueuedEventBus(dispatcher: $fakeDispatcher, handlers: []);

    $bus->dispatch($event);

    expect($fakeDispatcher->dispatched)->toBe([]);
});

it('dispatches jobs for multiple events', function (): void {
    $fakeDispatcher = new FakeDispatcher;

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

    $handler = new class implements DomainEventHandler
    {
        public function handle(DomainEvent $domainEvent): void {}
    };

    $bus = new QueuedEventBus(
        dispatcher: $fakeDispatcher,
        handlers: [
            $event1::class => [$handler::class],
            $event2::class => [$handler::class],
        ],
    );

    $bus->dispatch($event1, $event2);

    expect($fakeDispatcher->dispatched)->toHaveCount(2);
});
