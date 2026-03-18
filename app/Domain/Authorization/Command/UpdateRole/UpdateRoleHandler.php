<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\UpdateRole;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Event\RoleUpdated;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermission;
use App\Domain\Authorization\RoleRepository;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateRoleCommand> */
final readonly class UpdateRoleHandler implements CommandHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->roleRepository->findById(new RoleId($command->id));

        if (! $existing instanceof Role) {
            throw new RoleNotFoundException($command->id);
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

        $updatedRole = new Role(
            id: $existing->id,
            organizationId: $existing->organizationId,
            name: new RoleName($command->name),
            description: $command->description,
            isSystem: $existing->isSystem,
            permissions: $rolePermissions,
        );

        $this->roleRepository->update($updatedRole);

        $this->eventCollector->collect(new RoleUpdated(
            roleId: $updatedRole->id->value,
            name: $updatedRole->name->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
