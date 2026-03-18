<?php

declare(strict_types=1);

namespace App\Contract\Query;

/**
 * @template TQuery of Query
 * @template TResult
 */
interface QueryHandler
{
    /**
     * @param  TQuery  $query
     * @return TResult
     */
    public function handle(Query $query): mixed;
}
