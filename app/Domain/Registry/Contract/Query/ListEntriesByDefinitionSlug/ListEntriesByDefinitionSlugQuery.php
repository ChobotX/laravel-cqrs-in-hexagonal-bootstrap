<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query\ListEntriesByDefinitionSlug;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * @implements Query<list<\App\Domain\Registry\Contract\Entry>>
 */
#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesByDefinitionSlugQuery implements Query
{
    public function __construct(
        public string $namespace,
        public string $slug,
    ) {}
}
