<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for update definition in the Registry bounded context; dispatched through the command bus.
 */
#[RequiresPermission('registry.definitions.update')]
final readonly class UpdateDefinitionCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Human-visible label or title. */
        public string $name,
    ) {}
}
