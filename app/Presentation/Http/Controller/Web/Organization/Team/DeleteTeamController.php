<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Organization\Command\DeleteTeam\DeleteTeamCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.management.delete')]
final readonly class DeleteTeamController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $organizationId, string $teamId): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteTeamCommand($teamId));

        return redirect()->route('teams.index', $organizationId)->with('success', __('messages.teams.deleted'));
    }
}
