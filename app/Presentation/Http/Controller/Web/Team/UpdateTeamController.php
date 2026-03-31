<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Label\Command\AssignLabel\AssignLabelCommand;
use App\Domain\Label\Command\RemoveLabel\RemoveLabelCommand;
use App\Domain\Label\Label;
use App\Domain\Label\Query\GetEntityLabels\GetEntityLabelsQuery;
use App\Presentation\Http\Request\Web\Team\UpdateTeamRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.management.update')]
final readonly class UpdateTeamController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(UpdateTeamRequest $updateTeamRequest): RedirectResponse
    {
        $updateTeamCommand = $updateTeamRequest->toCommand();
        $this->commandBus->dispatch($updateTeamCommand);

        $this->syncLabels($updateTeamRequest, $updateTeamCommand->id);

        return redirect()->route('teams.index')->with('success', __('messages.teams.updated'));
    }

    private function syncLabels(UpdateTeamRequest $updateTeamRequest, string $targetTeamId): void
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        if (! $this->authorizationChecker->can($currentUserId, 'labels.management.read')) {
            return;
        }

        /** @var list<string> $submittedLabelIds */
        $submittedLabelIds = $updateTeamRequest->input('labels', []);

        /** @var list<Label> $currentLabels */
        $currentLabels = $this->queryBus->dispatch(new GetEntityLabelsQuery($targetTeamId));
        $currentLabelIds = array_map(fn (Label $label): string => $label->id->value, $currentLabels);

        $toAdd = array_diff($submittedLabelIds, $currentLabelIds);
        $toRemove = array_diff($currentLabelIds, $submittedLabelIds);

        foreach ($toAdd as $labelId) {
            $this->commandBus->dispatch(new AssignLabelCommand($labelId, $targetTeamId, 'teams'));
        }

        foreach ($toRemove as $labelId) {
            $this->commandBus->dispatch(new RemoveLabelCommand($labelId, $targetTeamId));
        }
    }
}
