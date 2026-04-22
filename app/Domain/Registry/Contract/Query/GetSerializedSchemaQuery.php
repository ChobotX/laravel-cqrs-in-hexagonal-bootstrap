<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Registry\Contract\ValueObject\JsonSchema;

/**
 * Query for get serialized schema in the Registry bounded context; dispatched through the query bus.
 *
 * @implements Query<?JsonSchema>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class GetSerializedSchemaQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $definitionId,
    ) {}
}
