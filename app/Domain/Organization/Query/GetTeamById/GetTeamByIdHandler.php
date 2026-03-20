<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\GetTeamById;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Organization\Exception\TeamNotFoundException;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamRepository;

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
