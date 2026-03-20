<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Organization\Command\AddTeamMember\AddTeamMemberCommand;
use App\Domain\Organization\Command\RemoveTeamMember\RemoveTeamMemberCommand;
use App\Presentation\Http\Request\Web\Organization\ManageTeamMembersRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('teams.members.update')]
final readonly class ManageTeamMembersController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ManageTeamMembersRequest $manageTeamMembersRequest, string $organizationId, string $teamId): RedirectResponse
    {
        if ($manageTeamMembersRequest->action() === 'add_member') {
            $this->commandBus->dispatch(new AddTeamMemberCommand(
                userId: $manageTeamMembersRequest->userId(),
                teamId: $teamId,
            ));
        }

        if ($manageTeamMembersRequest->action() === 'remove_member') {
            $this->commandBus->dispatch(new RemoveTeamMemberCommand(
                userId: $manageTeamMembersRequest->userId(),
                teamId: $teamId,
            ));
        }

        return redirect()->route('teams.show', [$organizationId, $teamId])->with('success', __('messages.teams.members_updated'));
    }
}
