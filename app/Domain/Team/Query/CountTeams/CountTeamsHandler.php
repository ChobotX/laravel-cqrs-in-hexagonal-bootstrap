<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\CountTeams;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\TeamRepository;

/** @implements QueryHandler<CountTeamsQuery, int> */
final readonly class CountTeamsHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
    ) {}

    public function handle(Query $query): int
    {
        return $this->teamRepository->count();
    }
}
