<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;
use App\Domain\Team\Contract\Enum\TeamMembershipAction;

/**
 * Orchestrates adding or removing a single user on a team via one command per controller call.
 * Handler dispatches the matching {@see AddTeamMemberCommand} or {@see RemoveTeamMemberCommand}.
 */
#[RequiresPermission('teams.members.update')]
final readonly class ManageTeamMembershipCommand implements Command
{
    public function __construct(
        /** Team affected by the change. */
        public string $teamId,
        /** User joining or leaving the team. */
        public string $userId,
        /** Discriminator: Add or Remove. */
        public TeamMembershipAction $action,
    ) {}
}
