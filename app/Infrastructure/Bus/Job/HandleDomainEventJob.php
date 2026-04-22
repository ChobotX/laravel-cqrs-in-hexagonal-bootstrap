<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\Job;

use App\Application\Bus\SensitiveDataMasker;
use App\Contract\Attribute\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Logging\Logger;
use App\Contract\Tracing\TraceContext;
use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;
use App\Infrastructure\Bus\InvalidHandlerException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use ReflectionClass;
use Throwable;

final class HandleDomainEventJob implements ShouldQueue
{
    use Queueable;

    private const int NANOSECONDS_PER_MILLISECOND = 1_000_000;

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

        $logger = $container->make(Logger::class);
        $traceContext = $container->make(TraceContext::class);
        $context = $this->baseContext($traceContext);
        $startTime = (int) hrtime(true);

        $logger->debug('Domain event handler dispatching', [
            ...$context,
            'level' => 'debug',
            'data' => SensitiveDataMasker::mask($this->domainEvent),
        ]);

        try {
            $domainEventHandler->handle($this->domainEvent);
        } catch (Throwable $throwable) {
            $logger->error('Domain event handler failed', [
                ...$context,
                'level' => 'error',
                'duration_ms' => $this->durationMs($startTime),
                'exception' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }

        $logger->info('Domain event handled', [
            ...$context,
            'level' => 'info',
            'duration_ms' => $this->durationMs($startTime),
        ]);
    }

    public function failed(Throwable $throwable): void
    {
        $logger = app(Logger::class);
        $traceContext = app(TraceContext::class);

        $logger->error('Domain event handler failed permanently', [
            ...$this->baseContext($traceContext),
            'level' => 'error',
            'exception' => $throwable->getMessage(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function baseContext(TraceContext $traceContext): array
    {
        return [
            'trace_id' => $traceContext->traceId(),
            'event' => $this->domainEvent::class,
            'handler' => $this->handlerClass,
            'tenant' => $this->tenantSlug,
        ];
    }

    private function durationMs(int $startTime): float
    {
        return ((int) hrtime(true) - $startTime) / self::NANOSECONDS_PER_MILLISECOND;
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
