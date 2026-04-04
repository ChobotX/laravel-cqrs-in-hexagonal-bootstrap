<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\ValueObject;

use App\Domain\Team\Contract\Entity\Team;

final readonly class TeamTreeNode
{
    /** @param list<TeamMember> $members */
    public function __construct(
        public Team $team,
        public array $members,
    ) {}
}
