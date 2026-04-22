<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Domain\User\Contract\Command\ResetPasswordCommand;
use App\Presentation\Http\Request\Auth\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

#[SkipPermissionCheck('Guest reset password')]
final readonly class ResetPasswordController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(ResetPasswordRequest $resetPasswordRequest): RedirectResponse
    {
        $this->commandBus->dispatch(new ResetPasswordCommand(
            email: $resetPasswordRequest->string('email')->toString(),
            token: $resetPasswordRequest->string('token')->toString(),
            rawPassword: $resetPasswordRequest->string('password')->toString(),
        ));

        Auth::attempt([
            'email' => $resetPasswordRequest->string('email')->toString(),
            'password' => $resetPasswordRequest->string('password')->toString(),
        ]);

        $resetPasswordRequest->session()->regenerate();

        return redirect('/users')->with('success', __('messages.auth.password_reset_success'));
    }
}
