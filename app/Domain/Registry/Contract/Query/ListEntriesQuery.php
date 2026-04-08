<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterableQuery;
use App\Application\Pagination\PaginatedResult;
use App\Contract\Query\Query;
use App\Domain\Registry\Contract\Entity\Entry;

/**
 * @implements Query<PaginatedResult<Entry>>
 */
#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesQuery implements FilterableQuery, Query
{
    public const int DEFAULT_PER_PAGE = 15;

    /** @param list<Filter> $filters */
    public function __construct(
        public string $definitionId,
        public int $page = 1,
        public int $perPage = self::DEFAULT_PER_PAGE,
        private array $filters = [],
    ) {}

    /** @param list<Filter> $filters */
    public function withFilters(array $filters): static
    {
        return new self($this->definitionId, $this->page, $this->perPage, $filters);
    }

    /** @return list<Filter> */
    public function filters(): array
    {
        return $this->filters;
    }
}
