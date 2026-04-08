<?php

declare(strict_types=1);

namespace App\Application\Filtering;

/**
 * Queries implementing this interface carry filter parameters
 * from the Presentation layer. Follows the same withX()/x() pattern
 * as PaginableQuery, SortableQuery, and ScopeAwareQuery.
 *
 * Supports multiple filters via list<Filter>. Empty list = no filtering.
 */
interface FilterableQuery
{
    /** @param list<Filter> $filters */
    public function withFilters(array $filters): static;

    /** @return list<Filter> */
    public function filters(): array;
}
