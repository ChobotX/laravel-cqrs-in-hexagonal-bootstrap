<?php

declare(strict_types=1);

namespace App\Application\Sorting;

/**
 * Queries implementing this interface carry sorting parameters
 * from the Presentation layer. Follows the same withX()/x() pattern
 * as PaginableQuery and ScopeAwareQuery.
 *
 * Supports multi-column sorting via list<Sorting>. Empty list = no explicit sort.
 */
interface SortableQuery
{
    /** @param list<Sorting> $sortings */
    public function withSorting(array $sortings): static;

    /** @return list<Sorting> */
    public function sorting(): array;
}
