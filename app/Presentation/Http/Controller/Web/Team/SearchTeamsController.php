<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Query\SearchTeams\SearchTeamsQuery;
use App\Presentation\Http\Request\Web\Team\SearchTeamsRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('teams.management.read')]
final readonly class SearchTeamsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(SearchTeamsRequest $searchTeamsRequest): JsonResponse
    {
        /** @var list<Team> $teams */
        $teams = $this->queryBus->dispatch(new SearchTeamsQuery(
            term: $searchTeamsRequest->searchTerm(),
            excludeTeamIds: $searchTeamsRequest->excludeTeamIds(),
        ));

        $data = array_map(fn (Team $team): array => [
            'id' => $team->id->value,
            'name' => $team->name->value,
        ], $teams);

        return new JsonResponse(['data' => $data]);
    }
}
