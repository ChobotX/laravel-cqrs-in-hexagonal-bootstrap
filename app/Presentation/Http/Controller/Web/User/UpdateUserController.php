<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\Authorization\Contract\Command\SyncUserRolesCommand;
use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\Team\Contract\Command\SyncUserTeamsCommand;
use App\Domain\User\Contract\Command\SetPasswordCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Presentation\Http\Request\Web\User\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

#[RequiresPermission('users.list.update')]
final readonly class UpdateUserController
{
    private const string AVATAR_NAMESPACE = 'user-avatars';

    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(UpdateUserRequest $updateUserRequest): RedirectResponse
    {
        $avatarFileId = $this->resolveAvatarFileId($updateUserRequest);
        $updateUserCommand = $updateUserRequest->toCommand($avatarFileId);
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $this->commandBus->dispatch($updateUserCommand);

        $password = $updateUserRequest->string('password')->toString();

        if ($password !== '') {
            $this->commandBus->dispatch(new SetPasswordCommand($updateUserCommand->id, $password));
        }

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

    private function resolveAvatarFileId(UpdateUserRequest $updateUserRequest): ?string
    {
        if ($updateUserRequest->boolean('remove_avatar')) {
            return null;
        }

        $file = $updateUserRequest->file('avatar');

        if ($file instanceof UploadedFile) {
            $fileId = Str::uuid()->toString();

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

        /** @var User $currentUser */
        $currentUser = $this->queryBus->dispatch(new GetUserByIdQuery($updateUserRequest->routeString('userId')));

        return $currentUser->avatarFileId?->value;
    }
}
