<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Organization\Query\GetOrganizationById\GetOrganizationByIdQuery;
use App\Domain\Organization\Query\GetTeamById\GetTeamByIdQuery;
use App\Domain\Organization\Query\ListTeams\ListTeamsQuery;
use Illuminate\View\View;

#[RequiresPermission('teams.management.update')]
final readonly class ShowEditTeamController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $organizationId, string $teamId): View
    {
        $organization = $this->queryBus->dispatch(new GetOrganizationByIdQuery($organizationId));
        $team = $this->queryBus->dispatch(new GetTeamByIdQuery($teamId));
        $teams = $this->queryBus->dispatch(new ListTeamsQuery($organizationId));

        return view('teams.edit', [
            'organization' => $organization,
            'team' => $team,
            'teams' => $teams,
        ]);
    }
}
