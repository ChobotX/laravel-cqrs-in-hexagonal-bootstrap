<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\Job;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Tenancy\TenantBootstrapper;
use App\Infrastructure\Bus\InvalidHandlerException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class HandleDomainEventJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  class-string<DomainEventHandler>  $handlerClass
     */
    public function __construct(
        private readonly string $handlerClass,
        private readonly DomainEvent $domainEvent,
        private readonly ?string $tenantSlug = null,
    ) {}

    public function handle(Container $container): void
    {
        if ($this->tenantSlug !== null) {
            $container->make(TenantBootstrapper::class)->bootstrapBySlug($this->tenantSlug);
        }

        $domainEventHandler = $container->make($this->handlerClass);

        if (! $domainEventHandler instanceof DomainEventHandler) {
            throw InvalidHandlerException::expectedType($this->handlerClass, DomainEventHandler::class);
        }

        $domainEventHandler->handle($this->domainEvent);
    }
}
