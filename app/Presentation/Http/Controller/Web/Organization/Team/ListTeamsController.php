<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Organization\Query\GetOrganizationById\GetOrganizationByIdQuery;
use App\Domain\Organization\Query\ListTeams\ListTeamsQuery;
use Illuminate\View\View;

#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $organizationId): View
    {
        $organization = $this->queryBus->dispatch(new GetOrganizationByIdQuery($organizationId));
        $teams = $this->queryBus->dispatch(new ListTeamsQuery($organizationId));

        return view('teams.index', [
            'organization' => $organization,
            'teams' => $teams,
        ]);
    }
}
