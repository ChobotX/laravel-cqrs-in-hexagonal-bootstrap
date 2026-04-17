<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\Bus\CommandBus;
use App\Contract\Bus\BusMiddleware;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use Closure;
use Illuminate\Contracts\Container\Container;

use function array_reduce;
use function array_reverse;

final readonly class LaravelCommandBus implements CommandBus
{
    /**
     * @param  array<class-string<Command>, class-string<CommandHandler>>  $handlers
     * @param  list<BusMiddleware>  $middleware
     */
    public function __construct(
        private Container $container,
        private array $handlers,
        private array $middleware = [],
    ) {}

    public function dispatch(Command $command): void
    {
        $handlerClass = $this->handlers[$command::class]
            ?? throw HandlerNotFoundException::forCommand($command::class);

        $commandHandler = $this->container->make($handlerClass);

        if (! $commandHandler instanceof CommandHandler) {
            throw InvalidHandlerException::expectedType($handlerClass, CommandHandler::class);
        }

        $execute = static function (object $message) use ($commandHandler): null {
            if (! $message instanceof Command) {
                throw InvalidHandlerException::unexpectedMessageType($message::class, Command::class);
            }

            $commandHandler->handle($message);

            return null;
        };

        $pipeline = array_reduce(
            array_reverse($this->middleware),
            static fn (Closure $next, BusMiddleware $busMiddleware): Closure => static fn (object $message): mixed => $busMiddleware->handle($message, $next),
            $execute,
        );

        $pipeline($command);
    }
}
