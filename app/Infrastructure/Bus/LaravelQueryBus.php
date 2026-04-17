<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\Bus\QueryBus;
use App\Contract\Bus\BusMiddleware;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use Closure;
use Illuminate\Contracts\Container\Container;

use function array_reduce;
use function array_reverse;

final readonly class LaravelQueryBus implements QueryBus
{
    /**
     * @param  array<class-string<Query>, class-string<QueryHandler>>  $handlers
     * @param  list<BusMiddleware>  $middleware
     */
    public function __construct(
        private Container $container,
        private array $handlers,
        private array $middleware = [],
    ) {}

    /**
     * @template TResult
     *
     * @param  Query<TResult>  $query
     * @return TResult
     */
    public function dispatch(Query $query): mixed
    {
        $handlerClass = $this->handlers[$query::class]
            ?? throw HandlerNotFoundException::forQuery($query::class);

        $queryHandler = $this->container->make($handlerClass);

        if (! $queryHandler instanceof QueryHandler) {
            throw InvalidHandlerException::expectedType($handlerClass, QueryHandler::class);
        }

        $execute = static function (object $message) use ($queryHandler): mixed {
            if (! $message instanceof Query) {
                throw InvalidHandlerException::unexpectedMessageType($message::class, Query::class);
            }

            return $queryHandler->handle($message);
        };

        $pipeline = array_reduce(
            array_reverse($this->middleware),
            static fn (Closure $next, BusMiddleware $busMiddleware): Closure => static fn (object $message): mixed => $busMiddleware->handle($message, $next),
            $execute,
        );

        return $pipeline($query);
    }
}
