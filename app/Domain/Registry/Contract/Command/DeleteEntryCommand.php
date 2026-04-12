<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for delete entry in the Registry bounded context; dispatched through the command bus.
 */
#[RequiresPermission('registry.entries.delete')]
final readonly class DeleteEntryCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
