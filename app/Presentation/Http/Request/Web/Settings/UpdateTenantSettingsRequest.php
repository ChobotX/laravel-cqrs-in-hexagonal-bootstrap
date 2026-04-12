<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Presentation\Http\Request\FormRequest;
use Closure;

use function in_array;
use function is_string;
use function timezone_identifiers_list;

final class UpdateTenantSettingsRequest extends FormRequest
{
    /** @return array<string, array<int, string|Closure>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'display_timezone' => [
                'sometimes',
                'nullable',
                'max:64',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    if (! is_string($value) || ! in_array($value, timezone_identifiers_list(), true)) {
                        $fail(__('messages.settings.invalid_display_timezone'));
                    }
                },
            ],
        ];
    }
}
