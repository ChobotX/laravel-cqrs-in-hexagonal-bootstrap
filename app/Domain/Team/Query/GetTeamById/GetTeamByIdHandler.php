<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\GetTeamById;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Contract\Query\GetTeamById\GetTeamByIdQuery;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamRepository;
use App\Domain\Team\Exception\TeamNotFoundException;

/** @implements QueryHandler<GetTeamByIdQuery, Team> */
final readonly class GetTeamByIdHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
    ) {}

    public function handle(Query $query): Team
    {
        $team = $this->teamRepository->findById(new TeamId($query->id));

        if (! $team instanceof Team) {
            throw new TeamNotFoundException($query->id);
        }

        return $team;
    }
}
