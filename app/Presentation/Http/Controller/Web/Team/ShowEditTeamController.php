<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\Team\Query\GetTeamById\GetTeamByIdQuery;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use Illuminate\View\View;

#[RequiresPermission('teams.management.update')]
final readonly class ShowEditTeamController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(string $teamId): View
    {
        $team = $this->queryBus->dispatch(new GetTeamByIdQuery($teamId));
        $teams = $this->queryBus->dispatch(new ListTeamsQuery(
            $this->authenticatedUser->id() ?? '',
        ));

        return view('teams.edit', [
            'team' => $team,
            'teams' => $teams,
        ]);
    }
}
