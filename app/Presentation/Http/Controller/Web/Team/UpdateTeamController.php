<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\CommandBus;
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
        $this->commandBus->dispatch($updateTeamRequest->toCommand(
            $this->authenticatedUser->id() ?? '',
        ));

        return redirect()->route('teams.index')->with('success', __('messages.teams.updated'));
    }
}
