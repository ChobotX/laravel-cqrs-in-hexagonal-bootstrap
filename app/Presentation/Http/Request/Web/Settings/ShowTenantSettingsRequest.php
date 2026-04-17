<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Settings;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class ShowTenantSettingsRequest extends FormRequest
{
    use HandlesFormRequest;

    public const string PASSWORD_ROTATION_TAB = 'password-rotation';

    public const string TWO_FACTOR_TAB = 'two-factor';

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'tab' => ['nullable', 'in:'.self::PASSWORD_ROTATION_TAB.','.self::TWO_FACTOR_TAB],
        ];
    }

    public function activeTab(): string
    {
        $tab = $this->query('tab');

        if (! is_string($tab)) {
            return 'general';
        }

        if ($tab === self::PASSWORD_ROTATION_TAB) {
            return self::PASSWORD_ROTATION_TAB;
        }

        if ($tab === self::TWO_FACTOR_TAB) {
            return self::TWO_FACTOR_TAB;
        }

        return 'general';
    }
}
