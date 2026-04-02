<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Contract\Persistence\TransactionManager;
use Illuminate\Database\DatabaseManager;

final readonly class LaravelTransactionManager implements TransactionManager
{
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {}

    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public function transaction(callable $callback): mixed
    {
        return $this->databaseManager->connection()->transaction(static fn () => $callback());
    }
}
