<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for search labels in the Label bounded context; dispatched through the query bus.
 *
 * @implements Query<list<\App\Domain\Label\Contract\Entity\Label>>
 */
#[RequiresPermission('labels.management.read')]
final readonly class SearchLabelsQuery implements Query
{
    public const int DEFAULT_LIMIT = 50;

    /**
     * @param  list<string>  $excludeIds
     */
    public function __construct(
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Field `term` for this contract; see module docs for validation rules. */
        public string $term,
        /** List of stable identifiers for batch operations. */
        public array $excludeIds = [],
        /** Field `limit` for this contract; see module docs for validation rules. */
        public int $limit = self::DEFAULT_LIMIT,
    ) {}
}
