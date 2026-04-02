<?php

declare(strict_types=1);

namespace App\Contract\Bus;

use Closure;

interface Middleware
{
    /**
     * @template TResult
     *
     * @param  Closure(object): TResult  $next
     * @return TResult
     */
    public function handle(object $message, Closure $next): mixed;
}
