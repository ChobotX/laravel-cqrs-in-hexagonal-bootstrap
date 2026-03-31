<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Label\Label;
use App\Domain\Label\Query\GetEntityLabels\GetEntityLabelsQuery;
use App\Domain\Team\Query\GetTeamById\GetTeamByIdQuery;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use Illuminate\View\View;

#[RequiresPermission('teams.management.update')]
final readonly class ShowEditTeamController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(string $teamId): View
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $team = $this->queryBus->dispatch(new GetTeamByIdQuery($teamId));
        $paginatedResult = $this->queryBus->dispatch(new ListTeamsQuery($currentUserId));

        $canManageLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.read');
        $canCreateLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.create');

        $teamLabels = [];

        if ($canManageLabels) {
            /** @var list<Label> $labels */
            $labels = $this->queryBus->dispatch(new GetEntityLabelsQuery($teamId));
            $teamLabels = array_map(fn (Label $label): array => [
                'id' => $label->id->value,
                'name' => $label->name->value,
            ], $labels);
        }

        return view('teams.edit', [
            'team' => $team,
            'teams' => $paginatedResult->items,
            'canManageLabels' => $canManageLabels,
            'canCreateLabels' => $canCreateLabels,
            'teamLabels' => $teamLabels,
        ]);
    }
}
