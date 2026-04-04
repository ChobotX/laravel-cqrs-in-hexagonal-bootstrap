<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetDefinitionById;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * @implements Query<?\App\Domain\Registry\Contract\Definition>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class GetDefinitionByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
