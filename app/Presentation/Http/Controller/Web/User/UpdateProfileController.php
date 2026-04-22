<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\CommandBus;
use App\Presentation\Http\Request\Web\User\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck(reason: 'Profile update is available to all authenticated users')]
final readonly class UpdateProfileController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(UpdateProfileRequest $updateProfileRequest): RedirectResponse
    {
        $this->commandBus->dispatch($updateProfileRequest->toCommand(
            $this->authenticatedUser->id() ?? '',
        ));

        return redirect()->route('profile')->with('success', __('messages.profile.updated'));
    }
}
