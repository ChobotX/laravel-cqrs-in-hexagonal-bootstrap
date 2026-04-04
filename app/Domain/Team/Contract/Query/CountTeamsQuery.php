<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/** @implements Query<int> */
#[RequiresPermission('teams.management.read')]
final readonly class CountTeamsQuery implements Query {}
