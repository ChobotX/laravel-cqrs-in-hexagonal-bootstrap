<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Presentation\Http\Request\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class UpdateProfileRequest extends FormRequest
{
    /** @return array<string, array<string|\Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\Unique>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:10240'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ];
    }
}
