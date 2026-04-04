<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Contract\Query\CountTeamsQuery;
use App\Domain\Team\Contract\Repository\TeamRepository;

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
