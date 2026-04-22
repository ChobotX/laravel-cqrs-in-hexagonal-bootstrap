<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\User;

use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\User\Contract\Command\CreateUserWithAvatarCommand;
use App\Presentation\Http\Request\HandlesFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

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

    public function toCommand(string $id, string $uploadedBy): CreateUserWithAvatarCommand
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

        return new CreateUserWithAvatarCommand(
            id: $id,
            name: $this->string('name')->toString(),
            email: $this->string('email')->toString(),
            uploadedBy: $uploadedBy,
            avatarUpload: $avatarUpload,
        );
    }
}
