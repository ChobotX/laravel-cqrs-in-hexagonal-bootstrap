<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetSerializedSchema;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Registry\Schema\JsonSchema;

/**
 * @implements Query<?JsonSchema>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class GetSerializedSchemaQuery implements Query
{
    public function __construct(
        public string $definitionId,
    ) {}
}
