<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\AssignRoleToUser;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Event\RoleAssignedToUser;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Contract\UserPermissionRepository;
use App\Domain\Authorization\Exception\DuplicateRoleAssignmentException;
use App\Domain\Authorization\Exception\RoleNotFoundException;
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

        if ($this->userPermissionRepository->hasRole($command->userId, $roleId)) {
            throw new DuplicateRoleAssignmentException($command->userId, $command->roleId);
        }

        $this->userPermissionRepository->assignRole($command->userId, $roleId);

        $this->eventCollector->collect(new RoleAssignedToUser(
            userId: $command->userId,
            roleId: $command->roleId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
