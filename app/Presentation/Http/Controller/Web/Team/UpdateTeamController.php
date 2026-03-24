<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\Web\Team\UpdateTeamRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.management.update')]
final readonly class UpdateTeamController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateTeamRequest $updateTeamRequest): RedirectResponse
    {
        $this->commandBus->dispatch($updateTeamRequest->toCommand());

        return redirect()->route('teams.index')->with('success', __('messages.teams.updated'));
    }
}
