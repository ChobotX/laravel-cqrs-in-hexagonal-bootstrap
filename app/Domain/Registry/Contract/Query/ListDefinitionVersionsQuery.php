<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for list definition versions in the Registry bounded context; dispatched through the query bus.
 *
 * @implements Query<list<\App\Domain\Registry\Contract\Entity\DefinitionVersion>>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class ListDefinitionVersionsQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $definitionId,
    ) {}
}
