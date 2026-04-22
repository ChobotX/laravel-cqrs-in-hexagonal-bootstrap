<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Domain\Team\Contract\Command\ManageTeamMembershipCommand;
use App\Presentation\Http\Request\Web\Team\ManageTeamMembersRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.members.update')]
final readonly class ManageTeamMembersController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ManageTeamMembersRequest $manageTeamMembersRequest, string $teamId): RedirectResponse
    {
        $this->commandBus->dispatch(new ManageTeamMembershipCommand(
            teamId: $teamId,
            userId: $manageTeamMembersRequest->userId(),
            action: $manageTeamMembersRequest->action(),
        ));

        return redirect()->route('teams.show', $teamId)->with('success', __('messages.teams.members_updated'));
    }
}
