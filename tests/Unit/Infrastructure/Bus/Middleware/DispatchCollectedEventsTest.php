<?php

declare(strict_types=1);

use App\Application\Bus\EventBus;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EventCollector;
use App\Infrastructure\Bus\Middleware\DispatchCollectedEvents;

it('flushes collected events and dispatches them after handler success', function (): void {
    $event = new readonly class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $collector = new readonly class($event) implements EventCollector
    {
        public function __construct(private DomainEvent $domainEvent) {}

        public function collect(DomainEvent ...$events): void {}

        /** @return list<DomainEvent> */
        public function flush(): array
        {
            return [$this->domainEvent];
        }
    };

    $eventBus = new class implements EventBus
    {
        /** @var list<DomainEvent> */
        public array $dispatched = [];

        public function dispatch(DomainEvent ...$events): void
        {
            foreach ($events as $event) {
                $this->dispatched[] = $event;
            }
        }
    };

    $middleware = new DispatchCollectedEvents($collector, $eventBus);

    $handlerCalled = false;
    $result = $middleware->handle(new stdClass, static function () use (&$handlerCalled): string {
        $handlerCalled = true;

        return 'result';
    });

    expect($handlerCalled)->toBeTrue()
        ->and($result)->toBe('result')
        ->and($eventBus->dispatched)->toHaveCount(1)
        ->and($eventBus->dispatched[0])->toBe($event);
});

it('does not dispatch events when handler throws', function (): void {
    $collector = new class implements EventCollector
    {
        public function collect(DomainEvent ...$events): void {}

        /** @return list<DomainEvent> */
        public function flush(): array
        {
            return [];
        }
    };

    $eventBus = new class implements EventBus
    {
        /** @var list<DomainEvent> */
        public array $dispatched = [];

        public function dispatch(DomainEvent ...$events): void
        {
            foreach ($events as $event) {
                $this->dispatched[] = $event;
            }
        }
    };

    $middleware = new DispatchCollectedEvents($collector, $eventBus);

    expect(static fn (): mixed => $middleware->handle(new stdClass, static function (): never {
        throw new RuntimeException('Handler failed');
    }))->toThrow(RuntimeException::class);

    expect($eventBus->dispatched)->toBe([]);
});
