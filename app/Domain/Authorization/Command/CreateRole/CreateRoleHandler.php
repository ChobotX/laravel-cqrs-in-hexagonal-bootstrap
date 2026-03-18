<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\CreateRole;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Event\RoleCreated;
use App\Domain\Authorization\Exception\RoleAlreadyExistsException;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermission;
use App\Domain\Authorization\RoleRepository;
use DateTimeImmutable;

/** @implements CommandHandler<CreateRoleCommand> */
final readonly class CreateRoleHandler implements CommandHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $organizationId = $command->organizationId ?? '';

        if ($organizationId !== '') {
            $existing = $this->roleRepository->findByNameAndOrganization($command->name, $organizationId);

            if ($existing instanceof Role) {
                throw new RoleAlreadyExistsException($command->name);
            }
        }

        $rolePermissions = [];

        foreach ($command->permissions as $permissionData) {
            $parts = explode('.', $permissionData['permission']);
            $module = new Module($parts[0]);
            $feature = isset($parts[1]) ? new Feature($parts[1]) : null;
            $action = isset($parts[2]) ? Action::from($parts[2]) : null;

            $rolePermissions[] = new RolePermission(
                new PermissionKey($module, $feature, $action),
                AccessScope::from($permissionData['scope']),
            );
        }

        $role = new Role(
            id: new RoleId($command->id),
            organizationId: $command->organizationId,
            name: new RoleName($command->name),
            description: $command->description,
            isSystem: false,
            permissions: $rolePermissions,
        );

        $this->roleRepository->create($role);

        $this->eventCollector->collect(new RoleCreated(
            roleId: $role->id->value,
            organizationId: $role->organizationId,
            name: $role->name->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
