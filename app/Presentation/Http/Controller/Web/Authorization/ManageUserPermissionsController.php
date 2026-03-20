<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserCommand;
use App\Domain\Authorization\Command\RemovePermissionOverride\RemovePermissionOverrideCommand;
use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Command\SetPermissionOverride\SetPermissionOverrideCommand;
use App\Presentation\Http\Request\Web\Authorization\ManageUserPermissionsRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.roles.update')]
final readonly class ManageUserPermissionsController
{
    public function __construct(
        private CommandBus $commandBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(ManageUserPermissionsRequest $manageUserPermissionsRequest, string $userId): RedirectResponse
    {
        $organizationId = $this->organizationContext->currentOrganizationId();

        if ($manageUserPermissionsRequest->action() === 'assign_role') {
            $this->commandBus->dispatch(new AssignRoleToUserCommand(
                userId: $userId,
                roleId: $manageUserPermissionsRequest->string('role_id')->toString(),
                organizationId: $organizationId,
            ));
        }

        if ($manageUserPermissionsRequest->action() === 'revoke_role') {
            $this->commandBus->dispatch(new RevokeRoleFromUserCommand(
                userId: $userId,
                roleId: $manageUserPermissionsRequest->string('role_id')->toString(),
                organizationId: $organizationId,
            ));
        }

        if ($manageUserPermissionsRequest->action() === 'remove_override') {
            $this->commandBus->dispatch(new RemovePermissionOverrideCommand(
                userId: $userId,
                organizationId: $organizationId,
                permission: $manageUserPermissionsRequest->string('permission')->toString(),
            ));
        }

        if ($manageUserPermissionsRequest->action() === 'set_override') {
            $this->commandBus->dispatch(new SetPermissionOverrideCommand(
                userId: $userId,
                organizationId: $organizationId,
                permission: $manageUserPermissionsRequest->string('permission')->toString(),
                type: $manageUserPermissionsRequest->string('type')->toString(),
                scope: $manageUserPermissionsRequest->string('scope')->toString(),
            ));
        }

        return redirect()->route('users.permissions', $userId)->with('success', __('messages.permissions.updated'));
    }
}
