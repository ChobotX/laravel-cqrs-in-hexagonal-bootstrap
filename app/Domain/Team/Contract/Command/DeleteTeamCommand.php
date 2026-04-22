<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for delete team in the Team bounded context; dispatched through the command bus.
 */
#[RequiresPermission('teams.management.delete')]
final readonly class DeleteTeamCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
