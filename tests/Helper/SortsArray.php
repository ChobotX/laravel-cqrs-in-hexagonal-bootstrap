<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;

trait SortsArray
{
    /**
     * @template T
     *
     * @param  callable(T): (string|int|float)  $extractor
     */
    private static function compareValues(callable $extractor, mixed $a, mixed $b, SortDirection $sortDirection): int
    {
        $valueA = $extractor($a);
        $valueB = $extractor($b);
        $cmp = is_string($valueA) && is_string($valueB)
            ? strnatcasecmp($valueA, $valueB)
            : $valueA <=> $valueB;

        return $sortDirection === SortDirection::Desc ? -$cmp : $cmp;
    }

    /**
     * @template T
     *
     * @param  list<T>  $items
     * @param  list<Sorting>  $sortings
     * @param  callable(string): (callable(T): (string|int|float))  $extractorFactory
     * @return list<T>
     */
    private function sortArray(array $items, array $sortings, callable $extractorFactory): array
    {
        if ($sortings === []) {
            return $items;
        }

        usort($items, static function (mixed $a, mixed $b) use ($sortings, $extractorFactory): int {
            foreach ($sortings as $sorting) {
                $cmp = self::compareValues($extractorFactory($sorting->column), $a, $b, $sorting->direction);

                if ($cmp !== 0) {
                    return $cmp;
                }
            }

            return 0;
        });

        return $items;
    }
}
