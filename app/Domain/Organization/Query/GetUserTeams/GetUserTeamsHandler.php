<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\GetUserTeams;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamMemberRepository;
use App\Domain\Organization\TeamRepository;

/** @implements QueryHandler<GetUserTeamsQuery, list<Team>> */
final readonly class GetUserTeamsHandler implements QueryHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
        private TeamRepository $teamRepository,
    ) {}

    /** @return list<Team> */
    public function handle(Query $query): array
    {
        $teamIds = $this->teamMemberRepository->directMemberTeamIds($query->userId, $query->organizationId);

        if ($teamIds === []) {
            return [];
        }

        $organizationId = new OrganizationId($query->organizationId);
        $allTeams = $this->teamRepository->findAllByOrganization($organizationId);

        return array_values(array_filter(
            $allTeams,
            fn (Team $team): bool => in_array($team->id->value, $teamIds, true),
        ));
    }
}
