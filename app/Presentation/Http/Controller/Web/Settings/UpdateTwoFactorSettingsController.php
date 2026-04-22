<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Settings;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Domain\User\Contract\Command\UpdateTwoFactorSettingsCommand;
use App\Presentation\Http\Request\Web\Settings\UpdateTwoFactorSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Context;

#[RequiresPermission('settings.tenant.update')]
final readonly class UpdateTwoFactorSettingsController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateTwoFactorSettingsRequest $updateTwoFactorSettingsRequest): RedirectResponse
    {
        $tenantId = Context::get('tenant_id');

        if (! is_string($tenantId)) {
            abort(HttpStatus::FORBIDDEN);
        }

        $this->commandBus->dispatch(new UpdateTwoFactorSettingsCommand(
            requiredForAllUsers: $updateTwoFactorSettingsRequest->boolean('required_for_all_users'),
            emailOtpEnabled: $updateTwoFactorSettingsRequest->boolean('email_otp_enabled'),
            totpEnabled: $updateTwoFactorSettingsRequest->boolean('totp_enabled'),
        ));

        return redirect()->route('settings.index', ['tab' => 'two-factor'])
            ->with('success', __('messages.settings.two_factor_updated'));
    }
}
