<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Team\Contract\Entity\Team;

/**
 * Query for search teams in the Team bounded context; dispatched through the query bus.
 *
 * @implements Query<list<Team>>
 */
#[RequiresPermission('teams.management.read')]
final readonly class SearchTeamsQuery implements Query
{
    public const int DEFAULT_LIMIT = 50;

    /**
     * @param  list<string>  $excludeTeamIds
     */
    public function __construct(
        /** Field `term` for this contract; see module docs for validation rules. */
        public string $term,
        /** List of stable identifiers for batch operations. */
        public array $excludeTeamIds,
        /** Field `limit` for this contract; see module docs for validation rules. */
        public int $limit = self::DEFAULT_LIMIT,
    ) {}
}
