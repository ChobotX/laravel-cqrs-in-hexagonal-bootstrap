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
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleAssignmentPolicy;
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
    /** @param array<string, array{features: array<string, array{actions: list<string>}>}> $availableModules */
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private array $availableModules,
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

        return redirect()->route('profile')->with('success', __('messages.profile.updated'));
    }

    private function syncRoles(UpdateProfileRequest $updateProfileRequest, string $userId): void
    {
        if (! $this->authorizationChecker->can($userId, 'users.roles.update')) {
            return;
        }

        /** @var list<string> $submittedRoleIds */
        $submittedRoleIds = $updateProfileRequest->input('roles', []);

        $assignerPermissions = $this->queryBus->dispatch(new GetEffectivePermissionsQuery($userId));
        $isSuperAdmin = array_any($assignerPermissions, fn ($p): bool => $p->source === 'system:super-admin');

        $allRoles = $this->queryBus->dispatch(new ListRolesQuery);
        $roleAssignmentPolicy = new RoleAssignmentPolicy;
        $assignableRoles = $roleAssignmentPolicy->assignableRoles($assignerPermissions, $allRoles, $this->availableModules, $isSuperAdmin);
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
}
