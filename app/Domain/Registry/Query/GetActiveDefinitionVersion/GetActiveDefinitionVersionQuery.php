<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetActiveDefinitionVersion;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * @implements Query<?\App\Domain\Registry\DefinitionVersion>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class GetActiveDefinitionVersionQuery implements Query
{
    public function __construct(
        public string $definitionId,
    ) {}
}
