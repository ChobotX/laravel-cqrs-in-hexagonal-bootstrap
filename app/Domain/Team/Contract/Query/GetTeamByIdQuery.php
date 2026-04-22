<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Entity\Team;

/**
 * Query for get team by id in the Team bounded context; dispatched through the query bus.
 *
 * @implements Query<Team>
 */
#[RequiresPermission('teams.management.read')]
final readonly class GetTeamByIdQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
