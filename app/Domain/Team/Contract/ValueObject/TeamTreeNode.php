<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\ValueObject;

use App\Domain\Team\Contract\Entity\Team;

/**
 * Contract-level value object for team tree node used across Team commands, queries, and events.
 */
final readonly class TeamTreeNode
{
    /** @param list<TeamMember> $members */
    public function __construct(
        /** Field `team` for this contract; see module docs for validation rules. */
        public Team $team,
        /** Array for `members`; see constructor PHPDoc for structural tags when present. */
        public array $members,
    ) {}
}
