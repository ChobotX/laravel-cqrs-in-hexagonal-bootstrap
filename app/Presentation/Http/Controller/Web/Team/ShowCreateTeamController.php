<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use Illuminate\View\View;

#[RequiresPermission('teams.management.create')]
final readonly class ShowCreateTeamController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(): View
    {
        $teams = $this->queryBus->dispatch(new ListTeamsQuery(
            $this->authenticatedUser->id() ?? '',
        ));

        return view('teams.create', [
            'teams' => $teams,
        ]);
    }
}
