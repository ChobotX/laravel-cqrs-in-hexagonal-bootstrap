<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserCommand;
use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Query\GetAssignableRoles\GetAssignableRolesQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\File\Command\StoreAvatar\StoreAvatarCommand;
use App\Domain\File\FileName;
use App\Domain\File\FileUpload;
use App\Domain\File\MimeType;
use App\Domain\Label\Command\AssignLabel\AssignLabelCommand;
use App\Domain\Label\Command\RemoveLabel\RemoveLabelCommand;
use App\Domain\Label\Label;
use App\Domain\Label\Query\GetEntityLabels\GetEntityLabelsQuery;
use App\Domain\Team\Command\AddTeamMember\AddTeamMemberCommand;
use App\Domain\Team\Command\RemoveTeamMember\RemoveTeamMemberCommand;
use App\Domain\Team\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\Team\Team;
use App\Domain\User\Command\SetPassword\SetPasswordCommand;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use App\Domain\User\User;
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
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(UpdateUserRequest $updateUserRequest): RedirectResponse
    {
        $avatarFileId = $this->resolveAvatarFileId($updateUserRequest);
        $updateUserCommand = $updateUserRequest->toCommand($avatarFileId);

        $this->commandBus->dispatch($updateUserCommand);

        $password = $updateUserRequest->string('password')->toString();

        if ($password !== '') {
            $this->commandBus->dispatch(new SetPasswordCommand($updateUserCommand->id, $password));
        }

        $this->syncRoles($updateUserRequest, $updateUserCommand->id);
        $this->syncTeams($updateUserRequest, $updateUserCommand->id);
        $this->syncLabels($updateUserRequest, $updateUserCommand->id);

        return redirect()->route('users.edit', $updateUserCommand->id)->with('success', __('messages.users.updated'));
    }

    private function syncRoles(UpdateUserRequest $updateUserRequest, string $targetUserId): void
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        if (! $this->authorizationChecker->can($currentUserId, 'users.roles.update')) {
            return;
        }

        /** @var list<string> $submittedRoleIds */
        $submittedRoleIds = $updateUserRequest->input('roles', []);

        $assignableRoles = $this->queryBus->dispatch(new GetAssignableRolesQuery($currentUserId));
        $assignableRoleIds = array_map(fn (Role $role): string => $role->id->value, $assignableRoles);

        $currentUserRoles = $this->queryBus->dispatch(new GetUserRolesQuery($targetUserId));
        $currentRoleIds = array_map(fn (Role $role): string => $role->id->value, $currentUserRoles);

        $toAdd = array_diff($submittedRoleIds, $currentRoleIds);
        $toRemove = array_diff($currentRoleIds, $submittedRoleIds);

        foreach ($toAdd as $roleId) {
            if (in_array($roleId, $assignableRoleIds, true)) {
                $this->commandBus->dispatch(new AssignRoleToUserCommand($targetUserId, $roleId));
            }
        }

        foreach ($toRemove as $roleId) {
            if (in_array($roleId, $assignableRoleIds, true)) {
                $this->commandBus->dispatch(new RevokeRoleFromUserCommand($targetUserId, $roleId));
            }
        }
    }

    private function syncTeams(UpdateUserRequest $updateUserRequest, string $targetUserId): void
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        if (! $this->authorizationChecker->can($currentUserId, 'teams.members.update')) {
            return;
        }

        /** @var list<string> $submittedTeamIds */
        $submittedTeamIds = $updateUserRequest->input('teams', []);

        $currentTeams = $this->queryBus->dispatch(new GetUserTeamsQuery($targetUserId));
        $currentTeamIds = array_map(fn (Team $team): string => $team->id->value, $currentTeams);

        $toAdd = array_diff($submittedTeamIds, $currentTeamIds);
        $toRemove = array_diff($currentTeamIds, $submittedTeamIds);

        foreach ($toAdd as $teamId) {
            $this->commandBus->dispatch(new AddTeamMemberCommand($targetUserId, $teamId));
        }

        foreach ($toRemove as $teamId) {
            $this->commandBus->dispatch(new RemoveTeamMemberCommand($targetUserId, $teamId));
        }
    }

    private function syncLabels(UpdateUserRequest $updateUserRequest, string $targetUserId): void
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        if (! $this->authorizationChecker->can($currentUserId, 'labels.management.read')) {
            return;
        }

        /** @var list<string> $submittedLabelIds */
        $submittedLabelIds = $updateUserRequest->input('labels', []);

        /** @var list<Label> $currentLabels */
        $currentLabels = $this->queryBus->dispatch(new GetEntityLabelsQuery($targetUserId));
        $currentLabelIds = array_map(fn (Label $label): string => $label->id->value, $currentLabels);

        $toAdd = array_diff($submittedLabelIds, $currentLabelIds);
        $toRemove = array_diff($currentLabelIds, $submittedLabelIds);

        foreach ($toAdd as $labelId) {
            $this->commandBus->dispatch(new AssignLabelCommand($labelId, $targetUserId, 'users'));
        }

        foreach ($toRemove as $labelId) {
            $this->commandBus->dispatch(new RemoveLabelCommand($labelId, $targetUserId));
        }
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
