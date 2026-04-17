<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Override;

final class UpdatePasswordRotationSettingsRequest extends FormRequest
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
            'rotation_enabled' => ['sometimes', 'boolean'],
            'max_age_days' => ['nullable', 'required_if:rotation_enabled,true', 'integer', 'min:'.PasswordRotationSettings::MIN_PASSWORD_AGE_DAYS, 'max:'.PasswordRotationSettings::MAX_PASSWORD_AGE_DAYS],
            'history_count' => ['required', 'integer', 'min:'.PasswordRotationSettings::MIN_HISTORY_COUNT, 'max:'.PasswordRotationSettings::MAX_HISTORY_COUNT],
        ];
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'rotation_enabled' => $this->boolean('rotation_enabled'),
        ]);
    }
}
