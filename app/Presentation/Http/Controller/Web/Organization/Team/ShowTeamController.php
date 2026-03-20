<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Organization\Query\GetOrganizationById\GetOrganizationByIdQuery;
use App\Domain\Organization\Query\GetTeamById\GetTeamByIdQuery;
use App\Domain\Organization\Query\ListTeamMembers\ListTeamMembersQuery;
use Illuminate\View\View;

#[RequiresPermission('teams.management.read')]
final readonly class ShowTeamController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $organizationId, string $teamId): View
    {
        $organization = $this->queryBus->dispatch(new GetOrganizationByIdQuery($organizationId));
        $team = $this->queryBus->dispatch(new GetTeamByIdQuery($teamId));
        $members = $this->queryBus->dispatch(new ListTeamMembersQuery($teamId));

        return view('teams.show', [
            'organization' => $organization,
            'team' => $team,
            'members' => $members,
        ]);
    }
}
