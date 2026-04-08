<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Presentation\Http\Request\FormRequest;

final class UpdateTenantSettingsRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }
}
