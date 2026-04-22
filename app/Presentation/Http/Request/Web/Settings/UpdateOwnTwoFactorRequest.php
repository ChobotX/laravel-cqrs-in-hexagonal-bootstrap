<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Domain\User\Contract\Command\ManageOwnTwoFactorSettingsCommand;
use App\Domain\User\Contract\Enum\TwoFactorSettingsAction;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateOwnTwoFactorRequest extends FormRequest
{
    use HandlesFormRequest;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:email-save,totp-save,totp-confirm,totp-disable'],
            'email_two_factor_enabled' => ['required_if:action,email-save', 'boolean'],
            'totp_two_factor_enabled' => ['required_if:action,totp-save', 'boolean'],
            'totp_code' => ['nullable', 'string', 'size:6'],
        ];
    }

    public function action(): TwoFactorSettingsAction
    {
        return TwoFactorSettingsAction::from($this->string('action')->toString());
    }

    public function toCommand(string $userId): ManageOwnTwoFactorSettingsCommand
    {
        $action = $this->action();

        return new ManageOwnTwoFactorSettingsCommand(
            userId: $userId,
            action: $action,
            emailEnabled: $action === TwoFactorSettingsAction::EmailSave
                ? $this->boolean('email_two_factor_enabled')
                : null,
            totpEnabled: $action === TwoFactorSettingsAction::TotpSave
                ? $this->boolean('totp_two_factor_enabled')
                : null,
            totpCode: $action === TwoFactorSettingsAction::TotpConfirm
                ? $this->string('totp_code')->toString()
                : null,
        );
    }
}
