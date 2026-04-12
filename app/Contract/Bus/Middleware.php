<?php

declare(strict_types=1);

namespace App\Contract\Bus;

use Closure;

/**
 * Wraps command or query dispatch to add cross-cutting behavior (transactions, authorization, metrics).
 * Implementations call `$next` to continue the pipeline and must return its result unless short-circuiting.
 */
interface Middleware
{
    /**
     * Invoked once per dispatched message. `$message` is the concrete command or query object.
     *
     * @template TResult
     *
     * @param  Closure(object): TResult  $next
     * @return TResult
     */
    public function handle(object $message, Closure $next): mixed;
}
