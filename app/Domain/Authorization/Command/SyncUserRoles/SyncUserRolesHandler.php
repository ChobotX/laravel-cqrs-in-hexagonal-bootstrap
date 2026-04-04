<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\SyncUserRoles;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Command\SyncUserRoles\SyncUserRolesCommand;
use App\Domain\Authorization\Contract\Event\RoleAssignedToUser;
use App\Domain\Authorization\Contract\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Contract\UserPermissionRepository;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\RoleAssignmentPolicy;
use DateTimeImmutable;

use function array_diff;
use function array_map;
use function in_array;

/** @implements CommandHandler<SyncUserRolesCommand> */
final readonly class SyncUserRolesHandler implements CommandHandler
{
    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     */
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
        private RoleRepository $roleRepository,
        private PermissionResolver $permissionResolver,
        private RoleAssignmentPolicy $roleAssignmentPolicy,
        private EventCollector $eventCollector,
        private array $availableModules,
    ) {}

    public function handle(Command $command): void
    {
        if ($command->submittedRoleIds === null) {
            return;
        }

        $assignableRoleIds = $this->resolveAssignableRoleIds($command->actingUserId);

        $currentRoles = $this->userPermissionRepository->userRoles($command->targetUserId);
        $currentRoleIds = array_map(fn (Role $role): string => $role->id->value, $currentRoles);

        $toAdd = array_diff($command->submittedRoleIds, $currentRoleIds);
        $toRemove = array_diff($currentRoleIds, $command->submittedRoleIds);

        foreach ($toAdd as $roleId) {
            if (in_array($roleId, $assignableRoleIds, true)) {
                $this->userPermissionRepository->assignRole($command->targetUserId, new RoleId($roleId));
                $this->eventCollector->collect(new RoleAssignedToUser(
                    userId: $command->targetUserId,
                    roleId: $roleId,
                    occurredAt: new DateTimeImmutable,
                ));
            }
        }

        foreach ($toRemove as $roleId) {
            if (in_array($roleId, $assignableRoleIds, true)) {
                $this->userPermissionRepository->revokeRole($command->targetUserId, new RoleId($roleId));
                $this->eventCollector->collect(new RoleRevokedFromUser(
                    userId: $command->targetUserId,
                    roleId: $roleId,
                    occurredAt: new DateTimeImmutable,
                ));
            }
        }
    }

    /** @return list<string> */
    private function resolveAssignableRoleIds(string $actingUserId): array
    {
        $roles = $this->userPermissionRepository->userRoles($actingUserId);
        $overrides = $this->userPermissionRepository->userOverrides($actingUserId);
        $permissions = $this->permissionResolver->resolve($roles, $overrides, $this->availableModules);

        $isSuperAdmin = array_any($permissions, fn ($p): bool => $p->source === PermissionResolver::SUPER_ADMIN_SOURCE);

        $allRoles = $this->roleRepository->findAll();
        $assignable = $this->roleAssignmentPolicy->assignableRoles($permissions, $allRoles, $this->availableModules, $isSuperAdmin);

        return array_map(fn (Role $role): string => $role->id->value, $assignable);
    }
}
