<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\SeedDefaultRoles;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\IdGenerator;
use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Event\DefaultRolesSeeded;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermission;
use App\Domain\Authorization\RoleRepository;
use DateTimeImmutable;

/** @implements CommandHandler<SeedDefaultRolesCommand> */
final readonly class SeedDefaultRolesHandler implements CommandHandler
{
    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     */
    public function __construct(
        private RoleRepository $roleRepository,
        private EventCollector $eventCollector,
        private IdGenerator $idGenerator,
        private array $availableModules,
    ) {}

    public function handle(Command $command): void
    {
        $allPermissions = $this->buildAllPermissions();
        $readPermissions = $this->buildActionPermissions(Action::Read);
        $createUpdatePermissions = $this->buildActionPermissions(Action::Create, Action::Update);
        $readCreateUpdatePermissions = $this->buildActionPermissions(Action::Read, Action::Create, Action::Update);

        $roles = [
            $this->createRole('Manager', 'Full access within tenant', [
                [$allPermissions, AccessScope::All],
            ]),
            $this->createRole('Team Leader', 'Full access scoped to own team hierarchy', [
                [$allPermissions, AccessScope::Team],
            ]),
            $this->createRole('Team Member', 'Can view all, create and update own resources', [
                [$readPermissions, AccessScope::All],
                [$createUpdatePermissions, AccessScope::Own],
            ]),
            $this->createRole('Externist', 'External collaborator with own resource access', [
                [$readCreateUpdatePermissions, AccessScope::Own],
            ]),
        ];

        $roleIds = [];

        foreach ($roles as $role) {
            $this->roleRepository->create($role);
            $roleIds[] = $role->id->value;
        }

        $this->eventCollector->collect(new DefaultRolesSeeded(
            roleIds: $roleIds,
            occurredAt: new DateTimeImmutable,
        ));
    }

    /** @return list<PermissionKey> */
    private function buildAllPermissions(): array
    {
        $keys = [];

        foreach (array_keys($this->availableModules) as $moduleName) {
            $keys[] = new PermissionKey(new Module($moduleName));
        }

        return $keys;
    }

    /** @return list<PermissionKey> */
    private function buildActionPermissions(Action ...$actions): array
    {
        $keys = [];

        foreach ($this->availableModules as $moduleName => $moduleConfig) {
            foreach ($moduleConfig['features'] as $featureName => $featureConfig) {
                $this->collectMatchingActions($moduleName, $featureName, $featureConfig, $actions, $keys);
            }
        }

        return $keys;
    }

    /**
     * @param  array{actions: list<string>}  $featureConfig
     * @param  array<int|string, Action>  $actions
     * @param  list<PermissionKey>  $keys
     */
    private function collectMatchingActions(string $moduleName, string $featureName, array $featureConfig, array $actions, array &$keys): void
    {
        foreach ($actions as $action) {
            if (in_array($action->value, $featureConfig['actions'], true)) {
                $keys[] = new PermissionKey(
                    new Module($moduleName),
                    new Feature($featureName),
                    $action,
                );
            }
        }
    }

    /**
     * @param  list<array{0: list<PermissionKey>, 1: AccessScope}>  $permissionGroups
     */
    private function createRole(
        string $name,
        string $description,
        array $permissionGroups,
    ): Role {
        $permissions = [];

        foreach ($permissionGroups as [$permissionKeys, $accessScope]) {
            foreach ($permissionKeys as $permissionKey) {
                $permissions[] = new RolePermission($permissionKey, $accessScope);
            }
        }

        return new Role(
            id: new RoleId($this->idGenerator->generate()),
            name: new RoleName($name),
            description: $description,
            isSystem: false,
            permissions: $permissions,
        );
    }
}
