<?php

declare(strict_types=1);

namespace App\Application\Pagination;

/**
 * Queries implementing this interface carry pagination parameters
 * from the Presentation layer. Follows the same withX()/x() pattern
 * as ScopeAwareQuery.
 *
 * Implementors must also implement Query<PaginatedResult<T>>.
 */
interface PaginableQuery
{
    public function withPagination(Pagination $pagination): static;

    public function pagination(): ?Pagination;
}
