<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;

final class CreateUserRequest extends FormRequest
{
    use HandlesFormRequest;

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'avatar' => ['nullable', 'image', 'max:10240'],
        ];
    }

    public function toCommand(string $id, ?string $avatarFileId = null): CreateUserCommand
    {
        return new CreateUserCommand(
            id: $id,
            name: $this->string('name')->toString(),
            email: $this->string('email')->toString(),
            avatarFileId: $avatarFileId,
        );
    }
}
