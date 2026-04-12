<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for list entries by definition slug in the Registry bounded context; dispatched through the query bus.
 *
 * @implements Query<list<\App\Domain\Registry\Contract\Entity\Entry>>
 */
#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesByDefinitionSlugQuery implements Query
{
    public function __construct(
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public string $slug,
    ) {}
}
