<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\UpdateRole;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Command\UpdateRole\UpdateRoleCommand;
use App\Domain\Authorization\Contract\Event\RoleUpdated;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermissionMapper;
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

        $rolePermissionMapper = new RolePermissionMapper;
        $rolePermissions = array_map($rolePermissionMapper->map(...), $command->permissions);

        $updatedRole = new Role(
            id: $existing->id,
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
