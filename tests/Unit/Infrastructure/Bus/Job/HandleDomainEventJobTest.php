<?php

declare(strict_types=1);

use App\Application\Bus\Sensitive;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Logging\Logger;
use App\Contract\Tenancy\TenantBootstrapper;
use App\Contract\Tracing\TraceContext;
use App\Infrastructure\Bus\InvalidHandlerException;
use App\Infrastructure\Bus\Job\HandleDomainEventJob;
use Illuminate\Container\Container;
use Tests\Helper\FakeDomainEventHandler;

uses(Tests\TestCase::class);

it('resolves handler and calls it with the domain event', function (): void {
    $event = new class implements DomainEvent
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

    $handler = new FakeDomainEventHandler;
    $logger = createJobLoggerSpy();

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, $handler);
    $container->instance(Logger::class, $logger);
    $container->instance(TraceContext::class, createJobFakeTraceContext());

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->handle($container);

    expect($logger->infoCalls)->toHaveCount(1)
        ->and($logger->infoCalls[0]['message'])->toBe('Domain event handled');
});

it('throws InvalidHandlerException when container resolves wrong type', function (): void {
    $event = new class implements DomainEvent
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

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, new stdClass);

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->handle($container);
})->throws(InvalidHandlerException::class, 'is not an instance of');

it('bootstraps tenant when tenant slug is provided', function (): void {
    $event = new class implements DomainEvent
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

    $handler = new FakeDomainEventHandler;

    $bootstrapper = new class implements TenantBootstrapper
    {
        public ?string $bootstrappedSlug = null;

        public function bootstrapByDomain(string $domain): void {}

        public function bootstrapBySlug(string $slug): void
        {
            $this->bootstrappedSlug = $slug;
        }

        public function reset(): void {}
    };

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, $handler);
    $container->instance(TenantBootstrapper::class, $bootstrapper);
    $container->instance(Logger::class, createJobLoggerSpy());
    $container->instance(TraceContext::class, createJobFakeTraceContext());

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
        tenantSlug: 'acme',
    );

    $job->handle($container);

    expect($bootstrapper->bootstrappedSlug)->toBe('acme');
});

it('skips tenant bootstrap when tenant slug is null', function (): void {
    $event = new class implements DomainEvent
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

    $handler = new FakeDomainEventHandler;
    $logger = createJobLoggerSpy();

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, $handler);
    $container->instance(Logger::class, $logger);
    $container->instance(TraceContext::class, createJobFakeTraceContext());

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->handle($container);

    expect($logger->infoCalls)->toHaveCount(1)
        ->and($logger->infoCalls[0]['context']['tenant'])->toBeNull();
});

it('reads retry policy from handler attribute', function (): void {
    $event = new class implements DomainEvent
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

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    expect($job->tries)->toBe(1)
        ->and($job->backoff)->toBe([])
        ->and($job->timeout)->toBe(10);
});

it('throws when handler lacks RetryPolicy attribute', function (): void {
    $event = new class implements DomainEvent
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

    new HandleDomainEventJob(
        handlerClass: DomainEventHandler::class,
        domainEvent: $event,
    );
})->throws(InvalidHandlerException::class, 'must declare a #[RetryPolicy] attribute');

it('logs error when job fails permanently', function (): void {
    $event = new class implements DomainEvent
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

    $logger = createJobLoggerSpy();

    app()->instance(Logger::class, $logger);
    app()->instance(TraceContext::class, createJobFakeTraceContext('trace-permanent-fail'));

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->failed(new RuntimeException('Queue worker crashed'));

    expect($logger->errorCalls)->toHaveCount(1)
        ->and($logger->errorCalls[0]['message'])->toBe('Domain event handler failed permanently')
        ->and($logger->errorCalls[0]['context']['level'])->toBe('error')
        ->and($logger->errorCalls[0]['context']['trace_id'])->toBe('trace-permanent-fail');
});

it('logs info with context when handler executes successfully', function (): void {
    $event = new class implements DomainEvent
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

    $handler = new FakeDomainEventHandler;
    $logger = createJobLoggerSpy();

    $bootstrapper = new class implements TenantBootstrapper
    {
        public function bootstrapByDomain(string $domain): void {}

        public function bootstrapBySlug(string $slug): void {}

        public function reset(): void {}
    };

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, $handler);
    $container->instance(Logger::class, $logger);
    $container->instance(TenantBootstrapper::class, $bootstrapper);
    $container->instance(TraceContext::class, createJobFakeTraceContext('trace-success-123'));

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
        tenantSlug: 'acme',
    );

    $job->handle($container);

    expect($logger->infoCalls)->toHaveCount(1)
        ->and($logger->infoCalls[0]['message'])->toBe('Domain event handled')
        ->and($logger->infoCalls[0]['context']['level'])->toBe('info')
        ->and($logger->infoCalls[0]['context']['trace_id'])->toBe('trace-success-123')
        ->and($logger->infoCalls[0]['context']['handler'])->toBe(FakeDomainEventHandler::class)
        ->and($logger->infoCalls[0]['context']['tenant'])->toBe('acme')
        ->and($logger->infoCalls[0]['context']['duration_ms'])->toBeGreaterThanOrEqual(0.0);
});

it('logs error and re-throws when handler fails during execution', function (): void {
    $event = new class implements DomainEvent
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

    $logger = createJobLoggerSpy();

    $failingHandler = new #[App\Application\Event\RetryPolicy(tries: 1, backoff: [], timeout: 10)] class implements DomainEventHandler
    {
        public function handle(DomainEvent $domainEvent): void
        {
            throw new RuntimeException('Handler exploded');
        }
    };

    $container = new Container;
    $container->instance($failingHandler::class, $failingHandler);
    $container->instance(Logger::class, $logger);
    $container->instance(TraceContext::class, createJobFakeTraceContext('trace-fail-456'));

    $job = new HandleDomainEventJob(
        handlerClass: $failingHandler::class,
        domainEvent: $event,
    );

    expect(static fn () => $job->handle($container))->toThrow(RuntimeException::class, 'Handler exploded');

    expect($logger->errorCalls)->toHaveCount(1)
        ->and($logger->errorCalls[0]['message'])->toBe('Domain event handler failed')
        ->and($logger->errorCalls[0]['context']['level'])->toBe('error')
        ->and($logger->errorCalls[0]['context']['trace_id'])->toBe('trace-fail-456')
        ->and($logger->errorCalls[0]['context']['exception'])->toBe('Handler exploded')
        ->and($logger->errorCalls[0]['context']['duration_ms'])->toBeGreaterThanOrEqual(0.0);
});

it('logs debug with event data before handler execution', function (): void {
    $event = new class implements DomainEvent
    {
        public string $userId = 'user-abc';

        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }

        public function entityType(): string
        {
            return 'user';
        }

        public function entityId(): string
        {
            return $this->userId;
        }

        public function actionLabel(): string
        {
            return 'Stub';
        }
    };

    $handler = new FakeDomainEventHandler;
    $logger = createJobLoggerSpy();

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, $handler);
    $container->instance(Logger::class, $logger);
    $container->instance(TraceContext::class, createJobFakeTraceContext());

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->handle($container);

    $data = $logger->debugCalls[0]['context']['data'];
    assert(is_array($data));

    expect($logger->debugCalls)->toHaveCount(1)
        ->and($logger->debugCalls[0]['message'])->toBe('Domain event handler dispatching')
        ->and($logger->debugCalls[0]['context']['level'])->toBe('debug')
        ->and($data['userId'])->toBe('user-abc');
});

it('masks sensitive properties in debug event log', function (): void {
    $event = new class('user-abc', 's3cret-token') implements DomainEvent
    {
        public function __construct(
            public string $userId,
            #[Sensitive]
            public string $secretToken,
        ) {}

        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }

        public function entityType(): string
        {
            return 'user';
        }

        public function entityId(): string
        {
            return $this->userId;
        }

        public function actionLabel(): string
        {
            return 'Stub';
        }
    };

    $handler = new FakeDomainEventHandler;
    $logger = createJobLoggerSpy();

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, $handler);
    $container->instance(Logger::class, $logger);
    $container->instance(TraceContext::class, createJobFakeTraceContext());

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->handle($container);

    $data = $logger->debugCalls[0]['context']['data'];
    assert(is_array($data));

    expect($data['userId'])->toBe('user-abc')
        ->and($data['secretToken'])->toBe('***');
});

function createJobFakeTraceContext(
    ?string $traceId = null,
    ?string $userId = null,
    ?string $tenantId = null,
): TraceContext {
    return new readonly class($traceId, $userId, $tenantId) implements TraceContext
    {
        public function __construct(
            private ?string $traceId,
            private ?string $userId,
            private ?string $tenantId,
        ) {}

        public function traceId(): ?string
        {
            return $this->traceId;
        }

        public function userId(): ?string
        {
            return $this->userId;
        }

        public function tenantId(): ?string
        {
            return $this->tenantId;
        }
    };
}

/**
 * @return object{debugCalls: list<array{message: string, context: array<string, mixed>}>, infoCalls: list<array{message: string, context: array<string, mixed>}>, errorCalls: list<array{message: string, context: array<string, mixed>}>} & Logger
 */
function createJobLoggerSpy(): Logger
{
    return new class implements Logger
    {
        /** @var list<array{message: string, context: array<string, mixed>}> */
        public array $debugCalls = [];

        /** @var list<array{message: string, context: array<string, mixed>}> */
        public array $infoCalls = [];

        /** @var list<array{message: string, context: array<string, mixed>}> */
        public array $errorCalls = [];

        public function debug(string $message, array $context = []): void
        {
            $this->debugCalls[] = ['message' => $message, 'context' => $context];
        }

        public function info(string $message, array $context = []): void
        {
            $this->infoCalls[] = ['message' => $message, 'context' => $context];
        }

        public function warning(string $message, array $context = []): void {}

        public function error(string $message, array $context = []): void
        {
            $this->errorCalls[] = ['message' => $message, 'context' => $context];
        }
    };
}
