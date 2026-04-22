<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\CommandBus;
use App\Presentation\Http\Request\Web\User\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.list.update')]
final readonly class UpdateUserController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(UpdateUserRequest $updateUserRequest): RedirectResponse
    {
        $updateUserWithAvatarAndRelationsCommand = $updateUserRequest->toCommand($this->authenticatedUser->id() ?? '');

        $this->commandBus->dispatch($updateUserWithAvatarAndRelationsCommand);

        return redirect()->route('users.edit', $updateUserWithAvatarAndRelationsCommand->id)->with('success', __('messages.users.updated'));
    }
}
