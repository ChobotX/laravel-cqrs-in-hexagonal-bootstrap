<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Auth;

use App\Presentation\Http\Request\FormRequest;

final class ResetPasswordRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
