<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;

trait PaginatesArray
{
    /**
     * @template T
     *
     * @param  list<T>  $items
     * @return PaginatedResult<T>
     */
    private function paginateArray(array $items, Pagination $pagination): PaginatedResult
    {
        $total = count($items);
        $sliced = array_slice($items, $pagination->offset(), $pagination->perPage);

        return new PaginatedResult($sliced, $total, $pagination);
    }
}
