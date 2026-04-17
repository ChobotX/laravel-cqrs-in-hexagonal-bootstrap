<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Auth;

use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class VerifyTwoFactorChallengeRequest extends FormRequest
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
            'method' => ['required', 'string', 'in:email,totp'],
            'code' => ['required', 'string', 'size:6'],
        ];
    }
}
