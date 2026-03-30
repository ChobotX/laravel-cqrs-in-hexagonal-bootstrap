<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\GetTeamTree;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/** @implements Query<list<TeamTreeNode>> */
#[RequiresPermission('teams.management.read')]
final readonly class GetTeamTreeQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
