<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Infrastructure\Bus\InvalidHandlerException;
use App\Infrastructure\Bus\Job\HandleDomainEventJob;
use Illuminate\Container\Container;

it('resolves handler and calls it with the domain event', function (): void {
    $event = new class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $handler = new class implements DomainEventHandler
    {
        public bool $handled = false;

        public function handle(DomainEvent $domainEvent): void
        {
            $this->handled = true;
        }
    };

    $container = new Container;
    $container->instance($handler::class, $handler);

    $job = new HandleDomainEventJob(
        handlerClass: $handler::class,
        domainEvent: $event,
    );

    $job->handle($container);

    expect($handler->handled)->toBeTrue();
});

it('throws InvalidHandlerException when container resolves wrong type', function (): void {
    $event = new class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable;
        }
    };

    $legitimateHandler = new class implements DomainEventHandler
    {
        public function handle(DomainEvent $domainEvent): void {}
    };

    $container = new Container;
    $container->instance($legitimateHandler::class, new stdClass);

    $job = new HandleDomainEventJob(
        handlerClass: $legitimateHandler::class,
        domainEvent: $event,
    );

    $job->handle($container);
})->throws(InvalidHandlerException::class, 'is not an instance of');
