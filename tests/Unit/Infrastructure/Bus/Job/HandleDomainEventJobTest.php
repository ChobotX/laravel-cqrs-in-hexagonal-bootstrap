<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Contract\Tenancy\TenantBootstrapper;
use App\Infrastructure\Bus\InvalidHandlerException;
use App\Infrastructure\Bus\Job\HandleDomainEventJob;
use Illuminate\Container\Container;
use Tests\Helper\FakeDomainEventHandler;

it('resolves handler and calls it with the domain event', function (): void {
    $event = new class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $handler = new FakeDomainEventHandler;

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, $handler);

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->handle($container);
});

it('throws InvalidHandlerException when container resolves wrong type', function (): void {
    $event = new class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
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
    };

    $handler = new FakeDomainEventHandler;

    $container = new Container;
    $container->instance(FakeDomainEventHandler::class, $handler);

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->handle($container);
});

it('reads retry policy from handler attribute', function (): void {
    $event = new class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
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
    };

    new HandleDomainEventJob(
        handlerClass: App\Contract\Event\DomainEventHandler::class,
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
    };

    $logger = new class implements App\Contract\Logging\Logger
    {
        public ?string $lastMessage = null;

        public function info(string $message, array $context = []): void {}

        public function warning(string $message, array $context = []): void {}

        public function error(string $message, array $context = []): void
        {
            $this->lastMessage = $message;
        }

        public function debug(string $message, array $context = []): void {}
    };

    app()->instance(App\Contract\Logging\Logger::class, $logger);

    $job = new HandleDomainEventJob(
        handlerClass: FakeDomainEventHandler::class,
        domainEvent: $event,
    );

    $job->failed(new RuntimeException('Queue worker crashed'));

    expect($logger->lastMessage)->toBe('Domain event handler failed permanently');
});
