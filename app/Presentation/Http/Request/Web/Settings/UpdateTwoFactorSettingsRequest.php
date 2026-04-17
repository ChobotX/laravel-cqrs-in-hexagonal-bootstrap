<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Override;

final class UpdateTwoFactorSettingsRequest extends FormRequest
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
            'required_for_all_users' => ['sometimes', 'boolean'],
            'email_otp_enabled' => ['sometimes', 'boolean'],
            'totp_enabled' => ['sometimes', 'boolean'],
        ];
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'required_for_all_users' => $this->boolean('required_for_all_users'),
            'email_otp_enabled' => $this->boolean('email_otp_enabled'),
            'totp_enabled' => $this->boolean('totp_enabled'),
        ]);
    }
}
