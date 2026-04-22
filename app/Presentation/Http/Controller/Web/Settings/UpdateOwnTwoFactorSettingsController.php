<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Domain\User\Contract\Enum\TwoFactorSettingsAction;
use App\Presentation\Http\Request\Web\Settings\UpdateOwnTwoFactorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

#[SkipPermissionCheck('Authenticated users can manage own two-factor settings')]
final readonly class UpdateOwnTwoFactorSettingsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateOwnTwoFactorRequest $updateOwnTwoFactorRequest): RedirectResponse
    {
        $userId = (string) Auth::id();
        $action = $updateOwnTwoFactorRequest->action();

        $this->commandBus->dispatch($updateOwnTwoFactorRequest->toCommand($userId));

        if ($action === TwoFactorSettingsAction::TotpConfirm
            || ($action === TwoFactorSettingsAction::EmailSave && $updateOwnTwoFactorRequest->boolean('email_two_factor_enabled'))) {
            Session::put('two_factor_passed', true);
            Session::save();
        }

        return redirect()->route('profile.two-factor')->with('success', __('messages.settings.two_factor_updated'));
    }
}
