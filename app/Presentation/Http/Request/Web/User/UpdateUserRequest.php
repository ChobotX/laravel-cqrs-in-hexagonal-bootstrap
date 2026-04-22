<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\User\Contract\Command\UpdateUserWithAvatarAndRelationsCommand;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
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

    public function toCommand(string $actorId): UpdateUserWithAvatarAndRelationsCommand
    {
        $file = $this->file('avatar');
        $avatarUpload = $file instanceof UploadedFile
            ? new FileUpload(
                originalName: new FileName($file->getClientOriginalName()),
                mimeType: new MimeType($file->getMimeType() ?? 'image/jpeg'),
                sizeInBytes: (int) $file->getSize(),
                file: $file,
            )
            : null;

        /** @var list<string>|null $roleIds */
        $roleIds = $this->has('roles') ? $this->input('roles', []) : null;
        /** @var list<string>|null $teamIds */
        $teamIds = $this->has('teams') ? $this->input('teams', []) : null;
        /** @var list<string>|null $labelIds */
        $labelIds = $this->has('labels') ? $this->input('labels', []) : null;

        return new UpdateUserWithAvatarAndRelationsCommand(
            id: $this->routeString('userId'),
            name: $this->string('name')->toString(),
            email: $this->has('email') ? $this->string('email')->toString() : null,
            actorId: $actorId,
            avatarUpload: $avatarUpload,
            removeAvatar: $this->boolean('remove_avatar'),
            roleIds: $roleIds,
            teamIds: $teamIds,
            labelIds: $labelIds,
        );
    }
}
