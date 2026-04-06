<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\User\Contract\Command\AcceptInviteCommand;
use App\Presentation\Http\Request\Auth\AcceptInviteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

#[SkipPermissionCheck('Guest invite acceptance')]
final readonly class AcceptInviteController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(AcceptInviteRequest $acceptInviteRequest, string $userId): RedirectResponse
    {
        $this->commandBus->dispatch(new AcceptInviteCommand(
            userId: $userId,
            rawPassword: $acceptInviteRequest->string('password')->toString(),
        ));

        Auth::loginUsingId($userId);

        $acceptInviteRequest->session()->regenerate();

        return redirect('/users')->with('success', __('messages.auth.invite_accepted'));
    }
}
