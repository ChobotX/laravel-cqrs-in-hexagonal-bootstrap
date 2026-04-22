<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\IdGenerator;
use App\Domain\Authorization\Contract\Command\SyncUserRolesCommand;
use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\Team\Contract\Command\SyncUserTeamsCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Presentation\Http\Request\Web\User\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

#[RequiresPermission('users.list.update')]
final readonly class UpdateUserController
{
    private const string AVATAR_NAMESPACE = 'user-avatars';

    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(UpdateUserRequest $updateUserRequest): RedirectResponse
    {
        $userId = $updateUserRequest->routeString('userId');
        $currentUserId = $this->authenticatedUser->id() ?? '';

        /** @var User $existingUser */
        $existingUser = $this->queryBus->dispatch(new GetUserByIdQuery($userId));

        $avatarFileId = $this->resolveAvatarFileId($updateUserRequest, $existingUser);
        $email = $updateUserRequest->has('email')
            ? $updateUserRequest->string('email')->toString()
            : $existingUser->email->value;

        $updateUserCommand = $updateUserRequest->toCommand($email, $avatarFileId);

        $this->commandBus->dispatch($updateUserCommand);

        /** @var list<string>|null $submittedRoleIds */
        $submittedRoleIds = $updateUserRequest->has('roles') ? $updateUserRequest->input('roles', []) : null;
        $this->commandBus->dispatch(new SyncUserRolesCommand($updateUserCommand->id, $submittedRoleIds, $currentUserId));

        /** @var list<string>|null $submittedTeamIds */
        $submittedTeamIds = $updateUserRequest->has('teams') ? $updateUserRequest->input('teams', []) : null;
        $this->commandBus->dispatch(new SyncUserTeamsCommand($updateUserCommand->id, $submittedTeamIds, $currentUserId));

        /** @var list<string>|null $submittedLabelIds */
        $submittedLabelIds = $updateUserRequest->has('labels') ? $updateUserRequest->input('labels', []) : null;
        $this->commandBus->dispatch(new SyncEntityLabelsCommand($updateUserCommand->id, 'users', $submittedLabelIds, $currentUserId));

        return redirect()->route('users.edit', $updateUserCommand->id)->with('success', __('messages.users.updated'));
    }

    private function resolveAvatarFileId(UpdateUserRequest $updateUserRequest, User $existingUser): ?string
    {
        if ($updateUserRequest->boolean('remove_avatar')) {
            return null;
        }

        $file = $updateUserRequest->file('avatar');

        if ($file instanceof UploadedFile) {
            $fileId = $this->idGenerator->generate();

            $this->commandBus->dispatch(new StoreAvatarCommand(
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

        return $existingUser->avatarFileId?->value;
    }
}
