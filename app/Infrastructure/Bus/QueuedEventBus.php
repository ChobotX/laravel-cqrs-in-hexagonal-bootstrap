<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Contract\Bus\EventBus;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Tenancy\TenantContext;
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
        private TenantContext $tenantContext,
    ) {}

    public function dispatch(DomainEvent ...$events): void
    {
        $tenantSlug = $this->tenantContext->isResolved() ? $this->tenantContext->currentTenantSlug() : null;

        foreach ($events as $event) {
            $handlerClasses = $this->handlers[$event::class] ?? [];

            foreach ($handlerClasses as $handlerClass) {
                $this->dispatcher->dispatch(new HandleDomainEventJob($handlerClass, $event, $tenantSlug));
            }
        }
    }
}
