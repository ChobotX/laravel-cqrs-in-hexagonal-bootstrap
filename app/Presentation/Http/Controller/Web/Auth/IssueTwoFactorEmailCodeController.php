<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Domain\User\Contract\Command\IssueEmailTwoFactorChallengeCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

#[SkipPermissionCheck('Authenticated users can request two-factor challenge')]
final readonly class IssueTwoFactorEmailCodeController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(): RedirectResponse
    {
        $this->commandBus->dispatch(new IssueEmailTwoFactorChallengeCommand((string) Auth::id()));

        return redirect()->route('two-factor.challenge')->with('success', __('messages.auth.two_factor_code_sent'));
    }
}
