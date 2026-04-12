<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for get definition by id in the Registry bounded context; dispatched through the query bus.
 *
 * @implements Query<?\App\Domain\Registry\Contract\Entity\Definition>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class GetDefinitionByIdQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
