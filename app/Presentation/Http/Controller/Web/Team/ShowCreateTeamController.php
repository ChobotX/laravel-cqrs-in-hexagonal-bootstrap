<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\Team\Contract\Query\ListTeamsQuery;
use Illuminate\View\View;

#[RequiresPermission('teams.management.create')]
final readonly class ShowCreateTeamController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        $paginatedResult = $this->queryBus->dispatch(new ListTeamsQuery);

        return view('teams.create', [
            'teams' => $paginatedResult->items,
        ]);
    }
}
