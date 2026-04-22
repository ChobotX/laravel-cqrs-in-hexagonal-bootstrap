<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Presentation\Http\Request\Web\Team\CreateTeamRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.management.create')]
final readonly class CreateTeamController
{
    public function __construct(
        private CommandBus $commandBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(CreateTeamRequest $createTeamRequest): RedirectResponse
    {
        $this->commandBus->dispatch($createTeamRequest->toCommand($this->idGenerator->generate()));

        return redirect()->route('teams.index')->with('success', __('messages.teams.created'));
    }
}
