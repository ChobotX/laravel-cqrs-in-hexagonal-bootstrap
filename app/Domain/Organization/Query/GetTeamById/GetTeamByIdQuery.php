<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\GetTeamById;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Organization\Team;

/** @implements Query<Team> */
#[RequiresPermission('teams.management.read')]
final readonly class GetTeamByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
