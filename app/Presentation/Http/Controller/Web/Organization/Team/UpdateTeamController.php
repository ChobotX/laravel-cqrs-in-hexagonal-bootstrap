<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\Web\Organization\UpdateTeamRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.management.update')]
final readonly class UpdateTeamController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateTeamRequest $updateTeamRequest, string $organizationId): RedirectResponse
    {
        $this->commandBus->dispatch($updateTeamRequest->toCommand());

        return redirect()->route('teams.index', $organizationId)->with('success', __('messages.teams.updated'));
    }
}
