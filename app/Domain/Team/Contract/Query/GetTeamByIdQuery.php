<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Entity\Team;

/** @implements Query<Team> */
#[RequiresPermission('teams.management.read')]
final readonly class GetTeamByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
