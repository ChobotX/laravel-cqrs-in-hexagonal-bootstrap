<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Domain\User\Contract\Command\UpdateUserCommand;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string|\Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\Unique>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->routeString('userId'))],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['string', 'uuid'],
            'teams' => ['sometimes', 'array'],
            'teams.*' => ['string', 'uuid'],
            'labels' => ['sometimes', 'array'],
            'labels.*' => ['string', 'uuid'],
            'avatar' => ['nullable', 'image', 'max:10240'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ];
    }

    public function toCommand(string $email, ?string $avatarFileId = null): UpdateUserCommand
    {
        return new UpdateUserCommand(
            id: $this->routeString('userId'),
            name: $this->string('name')->toString(),
            email: $email,
            avatarFileId: $avatarFileId,
        );
    }
}
