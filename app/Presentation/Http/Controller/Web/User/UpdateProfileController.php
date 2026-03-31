<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserCommand;
use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Query\GetAssignableRoles\GetAssignableRolesQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Notification\Command\UpdateNotificationPreferences\UpdateNotificationPreferencesCommand;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Team\Command\AddTeamMember\AddTeamMemberCommand;
use App\Domain\Team\Command\RemoveTeamMember\RemoveTeamMemberCommand;
use App\Domain\Team\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\Team\Team;
use App\Domain\User\Command\SetPassword\SetPasswordCommand;
use App\Domain\User\Command\UpdateProfile\UpdateProfileCommand;
use App\Domain\User\Command\UpdateUser\UpdateUserCommand;
use App\Presentation\Http\Request\Web\User\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck(reason: 'Profile update is available to all authenticated users')]
final readonly class UpdateProfileController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(UpdateProfileRequest $updateProfileRequest): RedirectResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';

        $name = $updateProfileRequest->string('name')->toString();
        $password = $updateProfileRequest->string('password')->toString();

        if ($this->authorizationChecker->can($userId, 'users.list.update')) {
            $this->commandBus->dispatch(new UpdateUserCommand(
                id: $userId,
                name: $name,
                email: $updateProfileRequest->string('email')->toString(),
            ));

            if ($password !== '') {
                $this->commandBus->dispatch(new SetPasswordCommand($userId, $password));
            }
        } else {
            $this->commandBus->dispatch(new UpdateProfileCommand(
                userId: $userId,
                name: $name,
                rawPassword: $password !== '' ? $password : null,
            ));
        }

        $this->syncRoles($updateProfileRequest, $userId);
        $this->syncTeams($updateProfileRequest, $userId);
        $this->syncNotificationPreferences($updateProfileRequest, $userId);

        return redirect()->route('profile')->with('success', __('messages.profile.updated'));
    }

    private function syncRoles(UpdateProfileRequest $updateProfileRequest, string $userId): void
    {
        if (! $this->authorizationChecker->can($userId, 'users.roles.update')) {
            return;
        }

        /** @var list<string> $submittedRoleIds */
        $submittedRoleIds = $updateProfileRequest->input('roles', []);

        $assignableRoles = $this->queryBus->dispatch(new GetAssignableRolesQuery($userId));
        $assignableRoleIds = array_map(fn (Role $role): string => $role->id->value, $assignableRoles);

        $currentUserRoles = $this->queryBus->dispatch(new GetUserRolesQuery($userId));
        $currentRoleIds = array_map(fn (Role $role): string => $role->id->value, $currentUserRoles);

        $toAdd = array_diff($submittedRoleIds, $currentRoleIds);
        $toRemove = array_diff($currentRoleIds, $submittedRoleIds);

        foreach ($toAdd as $roleId) {
            if (in_array($roleId, $assignableRoleIds, true)) {
                $this->commandBus->dispatch(new AssignRoleToUserCommand($userId, $roleId));
            }
        }

        foreach ($toRemove as $roleId) {
            if (in_array($roleId, $assignableRoleIds, true)) {
                $this->commandBus->dispatch(new RevokeRoleFromUserCommand($userId, $roleId));
            }
        }
    }

    private function syncTeams(UpdateProfileRequest $updateProfileRequest, string $userId): void
    {
        if (! $this->authorizationChecker->can($userId, 'teams.members.update')) {
            return;
        }

        /** @var list<string> $submittedTeamIds */
        $submittedTeamIds = $updateProfileRequest->input('teams', []);

        $currentTeams = $this->queryBus->dispatch(new GetUserTeamsQuery($userId));
        $currentTeamIds = array_map(fn (Team $team): string => $team->id->value, $currentTeams);

        $toAdd = array_diff($submittedTeamIds, $currentTeamIds);
        $toRemove = array_diff($currentTeamIds, $submittedTeamIds);

        foreach ($toAdd as $teamId) {
            $this->commandBus->dispatch(new AddTeamMemberCommand($userId, $teamId));
        }

        foreach ($toRemove as $teamId) {
            $this->commandBus->dispatch(new RemoveTeamMemberCommand($userId, $teamId));
        }
    }

    private function syncNotificationPreferences(UpdateProfileRequest $updateProfileRequest, string $userId): void
    {
        /** @var array<string, array<string, string>>|null $submitted */
        $submitted = $updateProfileRequest->input('notification_preferences');

        if ($submitted === null) {
            return;
        }

        $preferences = [];

        foreach ($submitted as $level => $channels) {
            $channelList = [NotificationChannel::InApp->value];

            if (isset($channels['email']) && (bool) $channels['email']) {
                $channelList[] = NotificationChannel::Email->value;
            }

            $preferences[$level] = $channelList;
        }

        $this->commandBus->dispatch(new UpdateNotificationPreferencesCommand(
            userId: $userId,
            preferences: $preferences,
        ));
    }
}
