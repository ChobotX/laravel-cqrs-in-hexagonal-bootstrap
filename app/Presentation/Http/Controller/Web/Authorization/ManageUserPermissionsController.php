<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Domain\Authorization\Contract\Command\ManageUserPermissionsCommand;
use App\Presentation\Http\Request\Web\Authorization\ManageUserPermissionsRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.roles.update')]
final readonly class ManageUserPermissionsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ManageUserPermissionsRequest $manageUserPermissionsRequest, string $userId): RedirectResponse
    {
        $roleId = $manageUserPermissionsRequest->string('role_id')->toString();
        $permission = $manageUserPermissionsRequest->string('permission')->toString();
        $overrideType = $manageUserPermissionsRequest->string('type')->toString();
        $overrideScope = $manageUserPermissionsRequest->string('scope')->toString();

        $this->commandBus->dispatch(new ManageUserPermissionsCommand(
            userId: $userId,
            action: $manageUserPermissionsRequest->action(),
            roleId: $roleId !== '' ? $roleId : null,
            permission: $permission !== '' ? $permission : null,
            overrideType: $overrideType !== '' ? $overrideType : null,
            overrideScope: $overrideScope !== '' ? $overrideScope : null,
        ));

        return redirect()->back()->with('success', __('messages.permissions.updated'));
    }
}
