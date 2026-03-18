<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\Bus\EventBus;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Infrastructure\Bus\Job\HandleDomainEventJob;
use Illuminate\Contracts\Bus\Dispatcher;

final readonly class QueuedEventBus implements EventBus
{
    /**
     * @param  array<class-string<DomainEvent>, list<class-string<DomainEventHandler>>>  $handlers
     */
    public function __construct(
        private Dispatcher $dispatcher,
        private array $handlers,
    ) {}

    public function dispatch(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $handlerClasses = $this->handlers[$event::class] ?? [];

            foreach ($handlerClasses as $handlerClass) {
                $this->dispatcher->dispatch(new HandleDomainEventJob($handlerClass, $event));
            }
        }
    }
}
