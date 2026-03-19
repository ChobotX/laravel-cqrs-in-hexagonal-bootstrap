<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Organization\Command\AddMember\AddMemberCommand;
use App\Domain\Organization\Command\RemoveMember\RemoveMemberCommand;
use App\Presentation\Http\Request\Web\Organization\ManageUserOrganizationsRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('organizations.members.update')]
final readonly class ManageUserOrganizationsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ManageUserOrganizationsRequest $manageUserOrganizationsRequest, string $userId): RedirectResponse
    {
        if ($manageUserOrganizationsRequest->action() === 'add_organization') {
            $this->commandBus->dispatch(new AddMemberCommand(
                userId: $userId,
                organizationId: $manageUserOrganizationsRequest->organizationId(),
            ));
        }

        if ($manageUserOrganizationsRequest->action() === 'remove_organization') {
            $this->commandBus->dispatch(new RemoveMemberCommand(
                userId: $userId,
                organizationId: $manageUserOrganizationsRequest->organizationId(),
            ));
        }

        return redirect()->route('users.permissions', $userId)->with('success', __('messages.organizations.members_updated'));
    }
}
