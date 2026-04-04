<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Command\AddTeamMember;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('teams.members.update')]
final readonly class AddTeamMemberCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $teamId,
    ) {}
}
