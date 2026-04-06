<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\User\Contract\Command\RequestPasswordResetCommand;
use App\Presentation\Http\Request\Auth\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck('Guest forgot password')]
final readonly class ForgotPasswordController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ForgotPasswordRequest $forgotPasswordRequest): RedirectResponse
    {
        $this->commandBus->dispatch(new RequestPasswordResetCommand(
            email: $forgotPasswordRequest->string('email')->toString(),
        ));

        return back()->with('success', __('messages.auth.reset_link_sent'));
    }
}
