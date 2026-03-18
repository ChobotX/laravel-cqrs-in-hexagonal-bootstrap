<?php

declare(strict_types=1);

use App\Contract\Bus\Middleware;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Infrastructure\Bus\HandlerNotFoundException;
use App\Infrastructure\Bus\InvalidHandlerException;
use App\Infrastructure\Bus\LaravelCommandBus;
use Illuminate\Container\Container;

it('dispatches command to its registered handler', function (): void {
    $command = new class implements Command {};

    $handler = new class implements CommandHandler
    {
        public bool $handled = false;

        public function handle(Command $command): void
        {
            $this->handled = true;
        }
    };

    $container = new Container;
    $container->instance($handler::class, $handler);

    $bus = new LaravelCommandBus(
        container: $container,
        handlers: [$command::class => $handler::class],
    );

    $bus->dispatch($command);

    expect($handler->handled)->toBeTrue();
});

it('throws HandlerNotFoundException when no handler registered', function (): void {
    $command = new class implements Command {};

    $bus = new LaravelCommandBus(
        container: new Container,
        handlers: [],
    );

    $bus->dispatch($command);
})->throws(HandlerNotFoundException::class);

it('throws InvalidHandlerException when container resolves wrong type', function (): void {
    $command = new class implements Command {};

    $legitimateHandler = new class implements CommandHandler
    {
        public function handle(Command $command): void {}
    };

    $container = new Container;
    $container->instance($legitimateHandler::class, new stdClass);

    $bus = new LaravelCommandBus(
        container: $container,
        handlers: [$command::class => $legitimateHandler::class],
    );

    $bus->dispatch($command);
})->throws(InvalidHandlerException::class, 'is not an instance of');

it('throws InvalidHandlerException when middleware passes wrong message type', function (): void {
    $command = new class implements Command {};

    $handler = new class implements CommandHandler
    {
        public function handle(Command $command): void {}
    };

    $container = new Container;
    $container->instance($handler::class, $handler);

    $maliciousMiddleware = new class implements Middleware
    {
        public function handle(object $message, Closure $next): mixed
        {
            return $next(new stdClass);
        }
    };

    $bus = new LaravelCommandBus(
        container: $container,
        handlers: [$command::class => $handler::class],
        middleware: [$maliciousMiddleware],
    );

    $bus->dispatch($command);
})->throws(InvalidHandlerException::class, 'but expected an instance of');
