<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Organization\Command\AddMember\AddMemberCommand;
use App\Domain\Organization\Command\RemoveMember\RemoveMemberCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

#[RequiresPermission('organizations.members.update')]
final readonly class ManageUserOrganizationsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(Request $request, string $userId): RedirectResponse
    {
        $action = $request->string('_action')->toString();

        if ($action === 'add_organization') {
            $this->commandBus->dispatch(new AddMemberCommand(
                userId: $userId,
                organizationId: $request->string('organization_id')->toString(),
            ));
        }

        if ($action === 'remove_organization') {
            $this->commandBus->dispatch(new RemoveMemberCommand(
                userId: $userId,
                organizationId: $request->string('organization_id')->toString(),
            ));
        }

        return redirect()->route('users.permissions', $userId)->with('success', __('messages.organizations.members_updated'));
    }
}
