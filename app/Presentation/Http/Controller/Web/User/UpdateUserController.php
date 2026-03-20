<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserCommand;
use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleAssignmentPolicy;
use App\Domain\Organization\Command\AddMember\AddMemberCommand;
use App\Domain\Organization\Command\AddTeamMember\AddTeamMemberCommand;
use App\Domain\Organization\Command\RemoveMember\RemoveMemberCommand;
use App\Domain\Organization\Command\RemoveTeamMember\RemoveTeamMemberCommand;
use App\Domain\Organization\Organization;
use App\Domain\Organization\Query\GetUserOrganizations\GetUserOrganizationsQuery;
use App\Domain\Organization\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\Organization\Team;
use App\Domain\User\Command\SetPassword\SetPasswordCommand;
use App\Presentation\Http\Request\Web\User\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.list.update')]
final readonly class UpdateUserController
{
    /** @param array<string, array{features: array<string, array{actions: list<string>}>}> $availableModules */
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private array $availableModules,
    ) {}

    public function __invoke(UpdateUserRequest $updateUserRequest): RedirectResponse
    {
        $updateUserCommand = $updateUserRequest->toCommand();

        $this->commandBus->dispatch($updateUserCommand);

        $password = $updateUserRequest->string('password')->toString();

        if ($password !== '') {
            $this->commandBus->dispatch(new SetPasswordCommand($updateUserCommand->id, $password));
        }

        $this->syncRoles($updateUserRequest, $updateUserCommand->id);
        $this->syncOrganizations($updateUserRequest, $updateUserCommand->id);
        $this->syncTeams($updateUserRequest, $updateUserCommand->id);

        return redirect('/users')->with('success', __('messages.users.updated'));
    }

    private function syncRoles(UpdateUserRequest $updateUserRequest, string $targetUserId): void
    {
        $orgId = $this->organizationContext->currentOrganizationId();
        $currentUserId = $this->authenticatedUser->id() ?? '';

        if (! $this->authorizationChecker->can($currentUserId, $orgId, 'users.roles.update')) {
            return;
        }

        /** @var list<string> $submittedRoleIds */
        $submittedRoleIds = $updateUserRequest->input('roles', []);

        $assignerPermissions = $this->queryBus->dispatch(new GetEffectivePermissionsQuery($currentUserId, $orgId));
        $isSuperAdmin = array_any($assignerPermissions, fn ($p): bool => $p->source === 'system:super-admin');

        $allRoles = $this->queryBus->dispatch(new ListRolesQuery($orgId));
        $roleAssignmentPolicy = new RoleAssignmentPolicy;
        $assignableRoles = $roleAssignmentPolicy->assignableRoles($assignerPermissions, $allRoles, $this->availableModules, $isSuperAdmin);
        $assignableRoleIds = array_map(fn (Role $role): string => $role->id->value, $assignableRoles);

        $currentUserRoles = $this->queryBus->dispatch(new GetUserRolesQuery($targetUserId, $orgId));
        $currentRoleIds = array_map(fn (Role $role): string => $role->id->value, $currentUserRoles);

        $toAdd = array_diff($submittedRoleIds, $currentRoleIds);
        $toRemove = array_diff($currentRoleIds, $submittedRoleIds);

        foreach ($toAdd as $roleId) {
            if (in_array($roleId, $assignableRoleIds, true)) {
                $this->commandBus->dispatch(new AssignRoleToUserCommand($targetUserId, $roleId, $orgId));
            }
        }

        foreach ($toRemove as $roleId) {
            if (in_array($roleId, $assignableRoleIds, true)) {
                $this->commandBus->dispatch(new RevokeRoleFromUserCommand($targetUserId, $roleId, $orgId));
            }
        }
    }

    private function syncOrganizations(UpdateUserRequest $updateUserRequest, string $targetUserId): void
    {
        $orgId = $this->organizationContext->currentOrganizationId();
        $currentUserId = $this->authenticatedUser->id() ?? '';

        if (! $this->authorizationChecker->can($currentUserId, $orgId, 'organizations.members.update')) {
            return;
        }

        /** @var list<string> $submittedOrgIds */
        $submittedOrgIds = $updateUserRequest->input('organizations', []);

        $currentOrgs = $this->queryBus->dispatch(new GetUserOrganizationsQuery($targetUserId));
        $currentOrgIds = array_map(fn (Organization $organization): string => $organization->id->value, $currentOrgs);

        $toAdd = array_diff($submittedOrgIds, $currentOrgIds);
        $toRemove = array_diff($currentOrgIds, $submittedOrgIds);

        foreach ($toAdd as $addOrgId) {
            $this->commandBus->dispatch(new AddMemberCommand($targetUserId, $addOrgId));
        }

        foreach ($toRemove as $removeOrgId) {
            $this->commandBus->dispatch(new RemoveMemberCommand($targetUserId, $removeOrgId));
        }
    }

    private function syncTeams(UpdateUserRequest $updateUserRequest, string $targetUserId): void
    {
        $orgId = $this->organizationContext->currentOrganizationId();
        $currentUserId = $this->authenticatedUser->id() ?? '';

        if (! $this->authorizationChecker->can($currentUserId, $orgId, 'teams.members.update')) {
            return;
        }

        /** @var list<string> $submittedTeamIds */
        $submittedTeamIds = $updateUserRequest->input('teams', []);

        $currentTeams = $this->queryBus->dispatch(new GetUserTeamsQuery($targetUserId, $orgId));
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
}
