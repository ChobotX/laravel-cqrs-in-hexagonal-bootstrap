<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Presentation\Http\Request\FormRequest;

final class ShowTenantSettingsRequest extends FormRequest
{
    public const string PASSWORD_ROTATION_TAB = 'password-rotation';

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'tab' => ['nullable', 'in:'.self::PASSWORD_ROTATION_TAB],
        ];
    }

    public function activeTab(): string
    {
        $tab = $this->query('tab');

        return is_string($tab) && $tab === self::PASSWORD_ROTATION_TAB
            ? self::PASSWORD_ROTATION_TAB
            : 'general';
    }
}
