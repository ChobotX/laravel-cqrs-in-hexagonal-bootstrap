<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;

trait FiltersArray
{
    /**
     * @template T
     *
     * @param  list<T>  $items
     * @param  list<Filter>  $filters
     * @param  callable(T, string): string  $valueExtractor
     * @param  callable(T, string): bool  $searchMatcher
     * @return list<T>
     */
    private function filterArray(array $items, array $filters, callable $valueExtractor, callable $searchMatcher): array
    {
        foreach ($filters as $filter) {
            if ($filter->column === '' && $filter->operator !== FilterOperator::Search) {
                continue;
            }

            /** @var string $stringValue */
            $stringValue = $filter->value;

            $items = match ($filter->operator) {
                FilterOperator::Equals => array_values(array_filter(
                    $items,
                    fn (mixed $item): bool => $valueExtractor($item, $filter->column) === $stringValue,
                )),
                FilterOperator::Contains => array_values(array_filter(
                    $items,
                    fn (mixed $item): bool => str_contains(
                        mb_strtolower($this->stripDiacriticsForFilter($valueExtractor($item, $filter->column))),
                        mb_strtolower($this->stripDiacriticsForFilter($stringValue)),
                    ),
                )),
                FilterOperator::In => is_array($filter->value) && $filter->value !== []
                    ? array_values(array_filter(
                        $items,
                        fn (mixed $item): bool => in_array($valueExtractor($item, $filter->column), $filter->value, true),
                    ))
                    : $items,
                FilterOperator::Search => $stringValue !== ''
                    ? array_values(array_filter(
                        $items,
                        fn (mixed $item): bool => $searchMatcher($item, $stringValue),
                    ))
                    : $items,
            };
        }

        return $items;
    }

    private function stripDiacriticsForFilter(string $value): string
    {
        $transliterated = transliterator_transliterate('Any-Latin; Latin-ASCII', $value);

        return $transliterated === false ? $value : $transliterated;
    }
}
