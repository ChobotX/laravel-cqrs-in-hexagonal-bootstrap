<?php

declare(strict_types=1);

namespace App\Domain\Team\Command\DeleteTeam;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('teams.management.delete')]
final readonly class DeleteTeamCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}
