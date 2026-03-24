<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\Web\Team\CreateTeamRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.management.create')]
final readonly class CreateTeamController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(CreateTeamRequest $createTeamRequest): RedirectResponse
    {
        $this->commandBus->dispatch($createTeamRequest->toCommand());

        return redirect()->route('teams.index')->with('success', __('messages.teams.created'));
    }
}
