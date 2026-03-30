<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\View\View;

#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): View
    {
        $paginatedResult = $this->queryBus->dispatch(new ListTeamsQuery(
            $this->authenticatedUser->id() ?? '',
            $paginationRequest->pagination(),
        ));

        return view('teams.index', [
            'result' => $paginatedResult,
        ]);
    }
}
