<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Query\GetEntryById;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * @implements Query<?\App\Domain\Registry\Contract\Entry>
 */
#[RequiresPermission('registry.entries.read')]
final readonly class GetEntryByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
