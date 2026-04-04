<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Team\Contract\Command\DeleteTeam\DeleteTeamCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.management.delete')]
final readonly class DeleteTeamController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $teamId): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteTeamCommand($teamId));

        return redirect()->route('teams.index')->with('success', __('messages.teams.deleted'));
    }
}
