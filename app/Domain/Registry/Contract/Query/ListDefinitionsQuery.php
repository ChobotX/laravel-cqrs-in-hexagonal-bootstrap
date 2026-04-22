<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Application\Pagination\PaginatedResult;
use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Registry\Contract\Entity\Definition;

/**
 * Query for list definitions in the Registry bounded context; dispatched through the query bus.
 *
 * @implements Query<PaginatedResult<Definition>>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class ListDefinitionsQuery implements Query
{
    public const int DEFAULT_PER_PAGE = 15;

    public function __construct(
        /** Field `page` for this contract; see module docs for validation rules. */
        public int $page = 1,
        /** Field `perPage` for this contract; see module docs for validation rules. */
        public int $perPage = self::DEFAULT_PER_PAGE,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public ?string $namespace = null,
    ) {}
}
