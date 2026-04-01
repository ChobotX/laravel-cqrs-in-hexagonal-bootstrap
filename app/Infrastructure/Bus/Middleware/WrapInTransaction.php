<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\Middleware;

use App\Application\Bus\SkipTransaction;
use App\Contract\Bus\Middleware;
use Closure;
use Illuminate\Database\DatabaseManager;
use ReflectionClass;

final readonly class WrapInTransaction implements Middleware
{
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {}

    public function handle(object $message, Closure $next): mixed
    {
        if (new ReflectionClass($message)->getAttributes(SkipTransaction::class) !== []) {
            return $next($message);
        }

        return $this->databaseManager->transaction(fn (): mixed => $next($message));
    }
}
