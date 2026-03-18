<?php

declare(strict_types=1);

namespace App\Contract\Bus;

use Closure;

interface Middleware
{
    public function handle(object $message, Closure $next): mixed;
}
