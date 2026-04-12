<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for activate definition version in the Registry bounded context; dispatched through the command bus.
 */
#[RequiresPermission('registry.definitions.update')]
final readonly class ActivateDefinitionVersionCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
