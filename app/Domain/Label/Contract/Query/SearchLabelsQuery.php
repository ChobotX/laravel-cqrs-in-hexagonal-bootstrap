<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * @implements Query<list<\App\Domain\Label\Contract\Label>>
 */
#[RequiresPermission('labels.management.read')]
final readonly class SearchLabelsQuery implements Query
{
    public const int DEFAULT_LIMIT = 50;

    /**
     * @param  list<string>  $excludeIds
     */
    public function __construct(
        public string $namespace,
        public string $term,
        public array $excludeIds = [],
        public int $limit = self::DEFAULT_LIMIT,
    ) {}
}
