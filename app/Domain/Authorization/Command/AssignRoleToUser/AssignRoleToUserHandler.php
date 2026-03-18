<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\AssignRoleToUser;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Event\RoleAssignedToUser;
use App\Domain\Authorization\Exception\DuplicateRoleAssignmentException;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleRepository;
use App\Domain\Authorization\UserPermissionRepository;
use DateTimeImmutable;

/** @implements CommandHandler<AssignRoleToUserCommand> */
final readonly class AssignRoleToUserHandler implements CommandHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
        private UserPermissionRepository $userPermissionRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $roleId = new RoleId($command->roleId);

        $role = $this->roleRepository->findById($roleId);

        if (! $role instanceof Role) {
            throw new RoleNotFoundException($command->roleId);
        }

        if ($this->userPermissionRepository->hasRole($command->userId, $roleId, $command->organizationId)) {
            throw new DuplicateRoleAssignmentException($command->userId, $command->roleId);
        }

        $this->userPermissionRepository->assignRole($command->userId, $roleId, $command->organizationId);

        $this->eventCollector->collect(new RoleAssignedToUser(
            userId: $command->userId,
            roleId: $command->roleId,
            organizationId: $command->organizationId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
