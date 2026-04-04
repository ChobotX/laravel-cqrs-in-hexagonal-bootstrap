<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\SearchTeams;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Team;

/** @implements Query<list<Team>> */
#[RequiresPermission('teams.management.read')]
final readonly class SearchTeamsQuery implements Query
{
    public const int DEFAULT_LIMIT = 50;

    /**
     * @param  list<string>  $excludeTeamIds
     */
    public function __construct(
        public string $term,
        public array $excludeTeamIds,
        public int $limit = self::DEFAULT_LIMIT,
    ) {}
}
