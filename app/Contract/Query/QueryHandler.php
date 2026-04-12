<?php

declare(strict_types=1);

namespace App\Contract\Query;

/**
 * Executes one concrete query type and returns its declared result. Handlers must be side-effect free aside from reads.
 *
 * @template TQuery of Query
 * @template TResult
 */
interface QueryHandler
{
    /**
     * Resolves the query without mutating domain state (no commands, no collected events).
     *
     * @param  TQuery  $query
     * @return TResult
     */
    public function handle(Query $query): mixed;
}
