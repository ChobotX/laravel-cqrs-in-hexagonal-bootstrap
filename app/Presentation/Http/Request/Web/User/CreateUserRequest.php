<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Presentation\Http\Request\FormRequest;
use Illuminate\Support\Str;

final class CreateUserRequest extends FormRequest
{
    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:10240'],
        ];
    }

    public function toCommand(?string $avatarFileId = null): CreateUserCommand
    {
        return new CreateUserCommand(
            id: Str::uuid()->toString(),
            name: $this->string('name')->toString(),
            email: $this->string('email')->toString(),
            avatarFileId: $avatarFileId,
        );
    }
}
