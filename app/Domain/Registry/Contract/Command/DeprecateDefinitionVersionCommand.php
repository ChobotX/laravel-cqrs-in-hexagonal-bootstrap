<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for deprecate definition version in the Registry bounded context; dispatched through the command bus.
 */
#[RequiresPermission('registry.definitions.update')]
final readonly class DeprecateDefinitionVersionCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
