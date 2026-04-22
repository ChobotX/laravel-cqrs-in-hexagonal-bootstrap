<?php

declare(strict_types=1);

namespace App\Contract\Bus;

use App\Contract\Query\Query;

interface QueryBus
{
    /**
     * @template TResult
     *
     * @param  Query<TResult>  $query
     * @return TResult
     */
    public function dispatch(Query $query): mixed;
}
