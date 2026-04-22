<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Domain\User\Contract\Command\VerifyTwoFactorChallengeCommand;
use App\Presentation\Http\Request\Web\Auth\VerifyTwoFactorChallengeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

#[SkipPermissionCheck('Authenticated users can submit two-factor challenge')]
final readonly class VerifyTwoFactorChallengeController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(VerifyTwoFactorChallengeRequest $verifyTwoFactorChallengeRequest): RedirectResponse
    {
        $this->commandBus->dispatch(new VerifyTwoFactorChallengeCommand(
            userId: (string) Auth::id(),
            method: $verifyTwoFactorChallengeRequest->string('method')->toString(),
            code: $verifyTwoFactorChallengeRequest->string('code')->toString(),
        ));

        session(['two_factor_passed' => true]);

        return redirect()->intended('/users');
    }
}
