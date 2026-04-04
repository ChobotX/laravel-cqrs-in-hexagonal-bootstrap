<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Contract\Query\SearchTeamsQuery;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamRepository;

/** @implements QueryHandler<SearchTeamsQuery, list<Team>> */
final readonly class SearchTeamsHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
    ) {}

    /** @return list<Team> */
    public function handle(Query $query): array
    {
        return $this->teamRepository->search(
            $query->term,
            $query->excludeTeamIds,
            $query->limit,
        );
    }
}
