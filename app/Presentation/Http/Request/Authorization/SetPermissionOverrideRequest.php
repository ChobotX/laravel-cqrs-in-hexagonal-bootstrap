<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Authorization;

use App\Presentation\Http\Request\FormRequest;

final class SetPermissionOverrideRequest extends FormRequest
{
    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'permission' => ['required', 'string'],
            'type' => ['required', 'in:grant,deny'],
            'scope' => ['required', 'in:all,team,own,shared'],
        ];
    }
}
