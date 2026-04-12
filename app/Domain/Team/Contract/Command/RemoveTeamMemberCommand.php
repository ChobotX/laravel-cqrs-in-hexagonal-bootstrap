<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('teams.members.update')]
final readonly class RemoveTeamMemberCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $teamId,
    ) {}
}
