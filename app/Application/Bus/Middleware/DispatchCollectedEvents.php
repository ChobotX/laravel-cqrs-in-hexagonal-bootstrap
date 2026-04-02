<?php

declare(strict_types=1);

namespace App\Application\Bus\Middleware;

use App\Application\Bus\EventBus;
use App\Contract\Bus\Middleware;
use App\Contract\Event\EventCollector;
use Closure;

final readonly class DispatchCollectedEvents implements Middleware
{
    public function __construct(
        private EventCollector $eventCollector,
        private EventBus $eventBus,
    ) {}

    /**
     * @template TResult
     *
     * @param  Closure(object): TResult  $next
     * @return TResult
     */
    public function handle(object $message, Closure $next): mixed
    {
        $result = $next($message);

        $this->eventBus->dispatch(...$this->eventCollector->flush());

        return $result;
    }
}
