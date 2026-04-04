<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Command\AssignRoleToUserCommand;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Event\RoleAssignedToUser;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
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
