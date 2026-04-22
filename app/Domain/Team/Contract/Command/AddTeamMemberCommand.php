<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for add team member in the Team bounded context; dispatched through the command bus.
 */
#[RequiresPermission('teams.members.update')]
final readonly class AddTeamMemberCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $teamId,
    ) {}
}
