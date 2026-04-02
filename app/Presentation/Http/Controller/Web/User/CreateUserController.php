<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\File\Command\StoreFile\StoreFileCommand;
use App\Domain\File\FileName;
use App\Domain\File\FileUpload;
use App\Domain\File\MimeType;
use App\Domain\User\Command\SetPassword\SetPasswordCommand;
use App\Presentation\Http\Request\Web\User\CreateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

#[RequiresPermission('users.list.create')]
final readonly class CreateUserController
{
    private const string AVATAR_NAMESPACE = 'user-avatars';

    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(CreateUserRequest $createUserRequest): RedirectResponse
    {
        $avatarFileId = $this->uploadAvatar($createUserRequest);

        $createUserCommand = $createUserRequest->toCommand($avatarFileId);

        $this->commandBus->dispatch($createUserCommand);
        $this->commandBus->dispatch(new SetPasswordCommand($createUserCommand->id, $createUserRequest->string('password')->toString()));

        return redirect('/users')->with('success', __('messages.users.created'));
    }

    private function uploadAvatar(CreateUserRequest $createUserRequest): ?string
    {
        $file = $createUserRequest->file('avatar');

        if (! $file instanceof UploadedFile) {
            return null;
        }

        $fileId = Str::uuid()->toString();

        $this->commandBus->dispatch(new StoreFileCommand(
            id: $fileId,
            namespace: self::AVATAR_NAMESPACE,
            uploadedBy: $this->authenticatedUser->id() ?? '',
            upload: new FileUpload(
                originalName: new FileName($file->getClientOriginalName()),
                mimeType: new MimeType($file->getMimeType() ?? 'image/jpeg'),
                sizeInBytes: (int) $file->getSize(),
                file: $file,
            ),
        ));

        return $fileId;
    }
}
