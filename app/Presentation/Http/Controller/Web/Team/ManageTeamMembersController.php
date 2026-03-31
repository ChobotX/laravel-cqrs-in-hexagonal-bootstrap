<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Team\Command\AddTeamMember\AddTeamMemberCommand;
use App\Domain\Team\Command\RemoveTeamMember\RemoveTeamMemberCommand;
use App\Presentation\Http\Request\Web\Team\ManageTeamMembersRequest;
use App\Presentation\Http\Request\Web\Team\TeamMemberAction;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.members.update')]
final readonly class ManageTeamMembersController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ManageTeamMembersRequest $manageTeamMembersRequest, string $teamId): RedirectResponse
    {
        if ($manageTeamMembersRequest->action() === TeamMemberAction::AddMember) {
            $this->commandBus->dispatch(new AddTeamMemberCommand(
                userId: $manageTeamMembersRequest->userId(),
                teamId: $teamId,
            ));
        }

        if ($manageTeamMembersRequest->action() === TeamMemberAction::RemoveMember) {
            $this->commandBus->dispatch(new RemoveTeamMemberCommand(
                userId: $manageTeamMembersRequest->userId(),
                teamId: $teamId,
            ));
        }

        return redirect()->route('teams.show', $teamId)->with('success', __('messages.teams.members_updated'));
    }
}
