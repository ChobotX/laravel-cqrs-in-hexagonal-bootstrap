<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query\ListDefinitions;

use App\Application\Authorization\RequiresPermission;
use App\Application\Pagination\PaginatedResult;
use App\Contract\Query\Query;
use App\Domain\Registry\Contract\Definition;

/**
 * @implements Query<PaginatedResult<Definition>>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class ListDefinitionsQuery implements Query
{
    public const int DEFAULT_PER_PAGE = 15;

    public function __construct(
        public int $page = 1,
        public int $perPage = self::DEFAULT_PER_PAGE,
        public ?string $namespace = null,
    ) {}
}
