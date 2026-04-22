<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\ValueObject\TeamTreeGridNode;

/**
 * Composed read-model query for the team tree grid. Handler dispatches GetTeamTreeQuery
 * (scope filtered to the acting user), prunes parent ids to visible subtree, and resolves
 * member roles when the acting user has users.roles.read.
 *
 * @implements Query<list<TeamTreeGridNode>>
 */
#[RequiresPermission('teams.management.read')]
final readonly class GetTeamTreeGridQuery implements Query
{
    public function __construct(
        public string $actingUserId,
    ) {}
}
