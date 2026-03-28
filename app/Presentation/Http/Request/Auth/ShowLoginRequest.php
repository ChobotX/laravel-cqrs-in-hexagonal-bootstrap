<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Auth;

use App\Presentation\Http\Request\FormRequest;

final class ShowLoginRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'redirect' => ['sometimes', 'nullable', 'string', 'max:2048'],
        ];
    }
}
