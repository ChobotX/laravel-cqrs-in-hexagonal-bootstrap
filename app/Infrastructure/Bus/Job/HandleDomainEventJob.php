<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\Job;

use App\Application\Event\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Logging\Logger;
use App\Contract\Tenancy\TenantBootstrapper;
use App\Infrastructure\Bus\InvalidHandlerException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use ReflectionClass;
use Throwable;

final class HandleDomainEventJob implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public int $tries;

    /** @var list<int> */
    public array $backoff;

    public int $timeout;

    /**
     * @param  class-string<DomainEventHandler>  $handlerClass
     */
    public function __construct(
        private readonly string $handlerClass,
        private readonly DomainEvent $domainEvent,
        private readonly ?string $tenantSlug = null,
    ) {
        $retryPolicy = $this->resolveRetryPolicy($handlerClass);
        $this->tries = $retryPolicy->tries;
        $this->backoff = $retryPolicy->backoff;
        $this->timeout = $retryPolicy->timeout;
    }

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

    public function failed(Throwable $throwable): void
    {
        $logger = app(Logger::class);

        $logger->error('Domain event handler failed permanently', [
            'handler' => $this->handlerClass,
            'event' => $this->domainEvent::class,
            'tenant' => $this->tenantSlug,
            'exception' => $throwable->getMessage(),
        ]);
    }

    /**
     * @param  class-string<DomainEventHandler>  $handlerClass
     */
    private function resolveRetryPolicy(string $handlerClass): RetryPolicy
    {
        $reflectionClass = new ReflectionClass($handlerClass);
        $attributes = $reflectionClass->getAttributes(RetryPolicy::class);

        if ($attributes === []) {
            throw InvalidHandlerException::missingRetryPolicy($handlerClass);
        }

        return $attributes[0]->newInstance();
    }
}
