<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetDefinitionBySlug;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * @implements Query<?\App\Domain\Registry\Contract\Definition>
 */
#[RequiresPermission('registry.definitions.read')]
final readonly class GetDefinitionBySlugQuery implements Query
{
    public function __construct(
        public string $namespace,
        public string $slug,
    ) {}
}
