<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for get definition by slug in the Registry bounded context; dispatched through the query bus.
 *
 * @implements Query<?\App\Domain\Registry\Contract\Entity\Definition>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class GetDefinitionBySlugQuery implements Query
{
    public function __construct(
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public string $slug,
    ) {}
}
