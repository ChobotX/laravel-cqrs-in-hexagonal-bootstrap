<?php

declare(strict_types=1);

namespace App\Application\Bus\Middleware;

use App\Contract\Attribute\SkipTransaction;
use App\Contract\Bus\BusMiddleware;
use App\Contract\Persistence\TransactionManager;
use Closure;
use ReflectionClass;

final readonly class WrapInTransaction implements BusMiddleware
{
    public function __construct(
        private TransactionManager $transactionManager,
    ) {}

    /**
     * @template TResult
     *
     * @param  Closure(object): TResult  $next
     * @return TResult
     */
    public function handle(object $message, Closure $next): mixed
    {
        if (new ReflectionClass($message)->getAttributes(SkipTransaction::class) !== []) {
            return $next($message);
        }

        return $this->transactionManager->transaction(fn (): mixed => $next($message));
    }
}
