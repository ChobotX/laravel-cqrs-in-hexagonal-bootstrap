<?php

declare(strict_types=1);

namespace App\Contract\Persistence;

/**
 * Runs work inside a database transaction boundary. Implementations commit on normal return and roll back on exceptions.
 */
interface TransactionManager
{
    /**
     * Executes `$callback` within a single transaction and returns its value.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public function transaction(callable $callback): mixed;
}
