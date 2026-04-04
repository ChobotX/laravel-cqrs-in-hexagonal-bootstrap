<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * @implements Query<list<\App\Domain\Registry\Contract\DefinitionVersion>>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class ListDefinitionVersionsQuery implements Query
{
    public function __construct(
        public string $definitionId,
    ) {}
}
