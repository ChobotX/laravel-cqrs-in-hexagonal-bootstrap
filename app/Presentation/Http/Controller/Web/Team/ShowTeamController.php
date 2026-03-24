<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Team\Query\GetTeamById\GetTeamByIdQuery;
use App\Domain\Team\Query\ListTeamMembers\ListTeamMembersQuery;
use Illuminate\View\View;

#[RequiresPermission('teams.management.read')]
final readonly class ShowTeamController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $teamId): View
    {
        $team = $this->queryBus->dispatch(new GetTeamByIdQuery($teamId));
        $members = $this->queryBus->dispatch(new ListTeamMembersQuery($teamId));

        return view('teams.show', [
            'team' => $team,
            'members' => $members,
        ]);
    }
}
