<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\ListTeams;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Team;
use App\Domain\Team\TeamRepository;

/** @implements QueryHandler<ListTeamsQuery, list<Team>> */
final readonly class ListTeamsHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
    ) {}

    /** @return list<Team> */
    public function handle(Query $query): array
    {
        return $this->teamRepository->findAll();
    }
}
