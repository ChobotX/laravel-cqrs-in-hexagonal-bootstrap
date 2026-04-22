<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for update team in the Team bounded context; dispatched through the command bus.
 */
#[RequiresPermission('teams.management.update')]
final readonly class UpdateTeamCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Human-visible label or title. */
        public string $name,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public string $slug,
        /** Longer human-readable explanation for admin UI or emails. */
        public string $description,
        /** Optional parentTeam identifier when absent. */
        public ?string $parentTeamId,
    ) {}
}
