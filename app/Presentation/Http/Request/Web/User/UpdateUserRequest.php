<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Domain\User\Command\UpdateUser\UpdateUserCommand;
use App\Presentation\Http\Request\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    /** @return array<string, array<string|\Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\Unique>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->routeString('userId'))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['string', 'uuid'],
            'organizations' => ['sometimes', 'array'],
            'organizations.*' => ['string', 'uuid'],
        ];
    }

    public function toCommand(): UpdateUserCommand
    {
        return new UpdateUserCommand(
            id: $this->routeString('userId'),
            name: $this->string('name')->toString(),
            email: $this->string('email')->toString(),
        );
    }
}
