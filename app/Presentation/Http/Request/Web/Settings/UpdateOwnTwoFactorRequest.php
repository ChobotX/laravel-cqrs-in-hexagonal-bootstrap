<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Presentation\Http\Request\FormRequest;

final class UpdateOwnTwoFactorRequest extends FormRequest
{
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
}
