<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Auth;

use App\Presentation\Http\Request\FormRequest;

final class LoginRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
