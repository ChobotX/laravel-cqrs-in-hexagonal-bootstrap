<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\Team\UpdateTeamRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.management.update')]
final readonly class UpdateTeamController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(UpdateTeamRequest $updateTeamRequest): RedirectResponse
    {
        $updateTeamCommand = $updateTeamRequest->toCommand();
        $this->commandBus->dispatch($updateTeamCommand);

        /** @var list<string>|null $submittedLabelIds */
        $submittedLabelIds = $updateTeamRequest->has('labels') ? $updateTeamRequest->input('labels', []) : null;
        $this->commandBus->dispatch(new SyncEntityLabelsCommand($updateTeamCommand->id, 'teams', $submittedLabelIds, $this->authenticatedUser->id() ?? ''));

        return redirect()->route('teams.index')->with('success', __('messages.teams.updated'));
    }
}
