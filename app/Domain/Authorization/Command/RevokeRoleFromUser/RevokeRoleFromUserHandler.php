<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\RevokeRoleFromUser;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Exception\RoleNotAssignedException;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\UserPermissionRepository;
use DateTimeImmutable;

/** @implements CommandHandler<RevokeRoleFromUserCommand> */
final readonly class RevokeRoleFromUserHandler implements CommandHandler
{
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $roleId = new RoleId($command->roleId);

        if (! $this->userPermissionRepository->hasRole($command->userId, $roleId)) {
            throw new RoleNotAssignedException($command->userId, $command->roleId);
        }

        $this->userPermissionRepository->revokeRole($command->userId, $roleId);

        $this->eventCollector->collect(new RoleRevokedFromUser(
            userId: $command->userId,
            roleId: $command->roleId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
