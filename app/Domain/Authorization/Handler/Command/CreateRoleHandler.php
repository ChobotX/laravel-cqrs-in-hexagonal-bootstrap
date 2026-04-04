<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Command\CreateRoleCommand;
use App\Domain\Authorization\Contract\Event\RoleCreated;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Exception\RoleAlreadyExistsException;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermissionMapper;
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
        $existing = $this->roleRepository->findByName($command->name);

        if ($existing instanceof Role) {
            throw new RoleAlreadyExistsException($command->name);
        }

        $rolePermissionMapper = new RolePermissionMapper;
        $rolePermissions = array_map($rolePermissionMapper->map(...), $command->permissions);

        $role = new Role(
            id: new RoleId($command->id),
            name: new RoleName($command->name),
            description: $command->description,
            isSystem: false,
            permissions: $rolePermissions,
        );

        $this->roleRepository->create($role);

        $this->eventCollector->collect(new RoleCreated(
            roleId: $role->id->value,
            name: $role->name->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
