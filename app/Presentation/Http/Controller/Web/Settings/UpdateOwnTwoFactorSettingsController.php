<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Domain\User\Contract\Command\ConfirmTotpSetupCommand;
use App\Domain\User\Contract\Command\DisableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\DisableTotpTwoFactorCommand;
use App\Domain\User\Contract\Command\EnableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\StartTotpSetupCommand;
use App\Domain\User\Contract\Query\GetTotpSetupQuery;
use App\Presentation\Http\Request\Web\Settings\UpdateOwnTwoFactorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

#[SkipPermissionCheck('Authenticated users can manage own two-factor settings')]
final readonly class UpdateOwnTwoFactorSettingsController
{
    private const string ACTION_EMAIL_SAVE = 'email-save';

    private const string ACTION_TOTP_SAVE = 'totp-save';

    private const string ACTION_TOTP_CONFIRM = 'totp-confirm';

    private const string ACTION_TOTP_DISABLE = 'totp-disable';

    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    public function __invoke(UpdateOwnTwoFactorRequest $updateOwnTwoFactorRequest): RedirectResponse
    {
        $userId = (string) Auth::id();
        $action = $updateOwnTwoFactorRequest->string('action')->toString();

        match ($action) {
            self::ACTION_EMAIL_SAVE => $updateOwnTwoFactorRequest->boolean('email_two_factor_enabled')
                ? $this->commandBus->dispatch(new EnableEmailTwoFactorCommand($userId))
                : $this->commandBus->dispatch(new DisableEmailTwoFactorCommand($userId)),
            self::ACTION_TOTP_SAVE => $this->applyTotpToggle($userId, $updateOwnTwoFactorRequest),
            self::ACTION_TOTP_CONFIRM => $this->commandBus->dispatch(new ConfirmTotpSetupCommand($userId, $updateOwnTwoFactorRequest->string('totp_code')->toString())),
            self::ACTION_TOTP_DISABLE => $this->commandBus->dispatch(new DisableTotpTwoFactorCommand($userId)),
            default => null,
        };

        if ($action === self::ACTION_TOTP_CONFIRM
            || ($action === self::ACTION_EMAIL_SAVE && $updateOwnTwoFactorRequest->boolean('email_two_factor_enabled'))) {
            Session::put('two_factor_passed', true);
            Session::save();
        }

        return redirect()->route('profile.two-factor')->with('success', __('messages.settings.two_factor_updated'));
    }

    private function applyTotpToggle(string $userId, UpdateOwnTwoFactorRequest $updateOwnTwoFactorRequest): void
    {
        $enabled = $updateOwnTwoFactorRequest->boolean('totp_two_factor_enabled');
        $totpSetup = $this->queryBus->dispatch(new GetTotpSetupQuery($userId));

        if ($enabled) {
            if ($totpSetup->secret === null) {
                $this->commandBus->dispatch(new StartTotpSetupCommand($userId));
            }

            return;
        }

        if ($totpSetup->secret !== null) {
            $this->commandBus->dispatch(new DisableTotpTwoFactorCommand($userId));
        }
    }
}
