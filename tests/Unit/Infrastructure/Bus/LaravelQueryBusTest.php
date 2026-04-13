<?php

declare(strict_types=1);

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Bus\Middleware;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Infrastructure\Bus\HandlerNotFoundException;
use App\Infrastructure\Bus\InvalidHandlerException;
use App\Infrastructure\Bus\LaravelQueryBus;
use Illuminate\Container\Container;

it('dispatches query and returns result', function (): void {
    $query = new LaravelQueryBusTestQuery;
    $handler = new LaravelQueryBusTestHandler;

    $container = new Container;
    $container->instance($handler::class, $handler);

    $bus = new LaravelQueryBus(
        container: $container,
        handlers: [$query::class => $handler::class],
    );

    expect($bus->dispatch($query))->toBe('query-result');
});

it('throws HandlerNotFoundException when no handler registered', function (): void {
    $query = new LaravelQueryBusTestQuery;

    $bus = new LaravelQueryBus(
        container: new Container,
        handlers: [],
    );

    $bus->dispatch($query);
})->throws(HandlerNotFoundException::class);

it('throws InvalidHandlerException when container resolves wrong type', function (): void {
    $query = new LaravelQueryBusTestQuery;
    $legitimateHandler = new LaravelQueryBusTestHandler;

    $container = new Container;
    $container->instance($legitimateHandler::class, new stdClass);

    $bus = new LaravelQueryBus(
        container: $container,
        handlers: [$query::class => $legitimateHandler::class],
    );

    $bus->dispatch($query);
})->throws(InvalidHandlerException::class, 'is not an instance of');

it('throws InvalidHandlerException when middleware passes wrong message type', function (): void {
    $query = new LaravelQueryBusTestQuery;
    $handler = new LaravelQueryBusTestHandler;

    $container = new Container;
    $container->instance($handler::class, $handler);

    $maliciousMiddleware = new class implements Middleware
    {
        public function handle(object $message, Closure $next): mixed
        {
            return $next(new stdClass);
        }
    };

    $bus = new LaravelQueryBus(
        container: $container,
        handlers: [$query::class => $handler::class],
        middleware: [$maliciousMiddleware],
    );

    $bus->dispatch($query);
})->throws(InvalidHandlerException::class, 'but expected an instance of');

/**
 * @implements Query<string>
 */
#[SkipPermissionCheck(reason: 'Test fixture query for infrastructure bus test')]
final class LaravelQueryBusTestQuery implements Query {}

/**
 * @implements QueryHandler<LaravelQueryBusTestQuery, string>
 */
final class LaravelQueryBusTestHandler implements QueryHandler
{
    public function handle(Query $query): string
    {
        return 'query-result';
    }
}
