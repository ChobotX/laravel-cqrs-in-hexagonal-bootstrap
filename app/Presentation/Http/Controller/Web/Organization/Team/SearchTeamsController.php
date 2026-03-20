<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Organization\Query\ListTeams\ListTeamsQuery;
use App\Domain\Organization\Team;
use App\Presentation\Http\Request\Web\Organization\SearchTeamsRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('teams.management.read')]
final readonly class SearchTeamsController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(SearchTeamsRequest $searchTeamsRequest): JsonResponse
    {
        $term = mb_strtolower($searchTeamsRequest->searchTerm());
        $excludeIds = $searchTeamsRequest->excludeTeamIds();
        $orgId = $this->organizationContext->currentOrganizationId();

        /** @var list<Team> $teams */
        $teams = $this->queryBus->dispatch(new ListTeamsQuery($orgId));

        $filtered = array_values(array_filter(
            $teams,
            fn (Team $team): bool => ! in_array($team->id->value, $excludeIds, true)
                && ($term === '' || str_contains(mb_strtolower($team->name->value), $term)),
        ));

        $data = array_map(fn (Team $team): array => [
            'id' => $team->id->value,
            'name' => $team->name->value,
        ], array_slice($filtered, 0, 50));

        return new JsonResponse(['data' => $data]);
    }
}
