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
final readonly class ManageOrganizationMembersController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(Request $request, string $organizationId): RedirectResponse
    {
        $action = $request->string('_action')->toString();

        if ($action === 'add_member') {
            $this->commandBus->dispatch(new AddMemberCommand(
                userId: $request->string('user_id')->toString(),
                organizationId: $organizationId,
            ));
        }

        if ($action === 'remove_member') {
            $this->commandBus->dispatch(new RemoveMemberCommand(
                userId: $request->string('user_id')->toString(),
                organizationId: $organizationId,
            ));
        }

        return redirect()->route('organizations.show', $organizationId)->with('success', __('messages.organizations.members_updated'));
    }
}
