<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Auth;

use App\Presentation\Http\Request\FormRequest;

final class VerifyTwoFactorChallengeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'method' => ['required', 'string', 'in:email,totp'],
            'code' => ['required', 'string', 'size:6'],
        ];
    }
}
