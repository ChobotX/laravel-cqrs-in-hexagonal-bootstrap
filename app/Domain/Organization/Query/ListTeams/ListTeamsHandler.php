<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\ListTeams;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamRepository;

/** @implements QueryHandler<ListTeamsQuery, list<Team>> */
final readonly class ListTeamsHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
    ) {}

    /** @return list<Team> */
    public function handle(Query $query): array
    {
        return $this->teamRepository->findAllByOrganization(new OrganizationId($query->organizationId));
    }
}
