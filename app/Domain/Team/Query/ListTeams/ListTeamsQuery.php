<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\ListTeams;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Team\Team;

/** @implements Query<list<Team>> */
#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsQuery implements Query {}
