<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Organization\Command\AddMember\AddMemberCommand;
use App\Domain\Organization\Command\RemoveMember\RemoveMemberCommand;
use App\Presentation\Http\Request\Web\Organization\ManageOrganizationMembersRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('organizations.members.update')]
final readonly class ManageOrganizationMembersController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ManageOrganizationMembersRequest $manageOrganizationMembersRequest, string $organizationId): RedirectResponse
    {
        if ($manageOrganizationMembersRequest->action() === 'add_member') {
            $this->commandBus->dispatch(new AddMemberCommand(
                userId: $manageOrganizationMembersRequest->userId(),
                organizationId: $organizationId,
            ));
        }

        if ($manageOrganizationMembersRequest->action() === 'remove_member') {
            $this->commandBus->dispatch(new RemoveMemberCommand(
                userId: $manageOrganizationMembersRequest->userId(),
                organizationId: $organizationId,
            ));
        }

        return redirect()->route('organizations.show', $organizationId)->with('success', __('messages.organizations.members_updated'));
    }
}
