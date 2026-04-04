<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\GetTeamTree;

use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamMember;

final readonly class TeamTreeNode
{
    /** @param list<TeamMember> $members */
    public function __construct(
        public Team $team,
        public array $members,
    ) {}
}
