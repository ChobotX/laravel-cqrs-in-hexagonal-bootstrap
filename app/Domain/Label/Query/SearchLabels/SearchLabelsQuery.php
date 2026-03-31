<?php

declare(strict_types=1);

namespace App\Domain\Label\Query\SearchLabels;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * @implements Query<list<\App\Domain\Label\Label>>
 */
#[RequiresPermission('labels.management.read')]
final readonly class SearchLabelsQuery implements Query
{
    /**
     * @param  list<string>  $excludeIds
     */
    public function __construct(
        public string $namespace,
        public string $term,
        public array $excludeIds = [],
        public int $limit = 50,
    ) {}
}
