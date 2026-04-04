<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\Authorization\Contract\Command\AssignRoleToUser\AssignRoleToUserCommand;
use App\Domain\Authorization\Contract\Command\RemovePermissionOverride\RemovePermissionOverrideCommand;
use App\Domain\Authorization\Contract\Command\RevokeRoleFromUser\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Contract\Command\SetPermissionOverride\SetPermissionOverrideCommand;
use App\Presentation\Http\Request\Web\Authorization\ManageUserPermissionsRequest;
use App\Presentation\Http\Request\Web\Authorization\UserPermissionAction;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.roles.update')]
final readonly class ManageUserPermissionsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ManageUserPermissionsRequest $manageUserPermissionsRequest, string $userId): RedirectResponse
    {
        if ($manageUserPermissionsRequest->action() === UserPermissionAction::AssignRole) {
            $this->commandBus->dispatch(new AssignRoleToUserCommand(
                userId: $userId,
                roleId: $manageUserPermissionsRequest->string('role_id')->toString(),
            ));
        }

        if ($manageUserPermissionsRequest->action() === UserPermissionAction::RevokeRole) {
            $this->commandBus->dispatch(new RevokeRoleFromUserCommand(
                userId: $userId,
                roleId: $manageUserPermissionsRequest->string('role_id')->toString(),
            ));
        }

        if ($manageUserPermissionsRequest->action() === UserPermissionAction::RemoveOverride) {
            $this->commandBus->dispatch(new RemovePermissionOverrideCommand(
                userId: $userId,
                permission: $manageUserPermissionsRequest->string('permission')->toString(),
            ));
        }

        if ($manageUserPermissionsRequest->action() === UserPermissionAction::SetOverride) {
            $this->commandBus->dispatch(new SetPermissionOverrideCommand(
                userId: $userId,
                permission: $manageUserPermissionsRequest->string('permission')->toString(),
                type: $manageUserPermissionsRequest->string('type')->toString(),
                scope: $manageUserPermissionsRequest->string('scope')->toString(),
            ));
        }

        return redirect()->back()->with('success', __('messages.permissions.updated'));
    }
}
