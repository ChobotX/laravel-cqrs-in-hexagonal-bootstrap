<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Authorization\Contract\Command\AssignRoleToUserCommand;
use App\Domain\Authorization\Contract\Command\ManageUserPermissionsCommand;
use App\Domain\Authorization\Contract\Command\RemovePermissionOverrideCommand;
use App\Domain\Authorization\Contract\Command\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Contract\Command\SetPermissionOverrideCommand;
use App\Domain\Authorization\Contract\Enum\UserPermissionAction;
use App\Domain\Authorization\Exception\InvalidUserPermissionActionPayloadException;

/** @implements CommandHandler<ManageUserPermissionsCommand> */
#[SkipDomainEvent(reason: 'Orchestrator — inner role and override handlers emit events')]
final readonly class ManageUserPermissionsHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(Command $command): void
    {
        $inner = match ($command->action) {
            UserPermissionAction::AssignRole => new AssignRoleToUserCommand(
                userId: $command->userId,
                roleId: $this->requireRoleId($command->roleId, UserPermissionAction::AssignRole),
            ),
            UserPermissionAction::RevokeRole => new RevokeRoleFromUserCommand(
                userId: $command->userId,
                roleId: $this->requireRoleId($command->roleId, UserPermissionAction::RevokeRole),
            ),
            UserPermissionAction::SetOverride => new SetPermissionOverrideCommand(
                userId: $command->userId,
                permission: $this->requirePermission($command->permission, UserPermissionAction::SetOverride),
                type: $this->requireOverrideType($command->overrideType),
                scope: $this->requireOverrideScope($command->overrideScope),
            ),
            UserPermissionAction::RemoveOverride => new RemovePermissionOverrideCommand(
                userId: $command->userId,
                permission: $this->requirePermission($command->permission, UserPermissionAction::RemoveOverride),
            ),
        };

        $this->commandBus->dispatch($inner);
    }

    private function requireRoleId(?string $roleId, UserPermissionAction $userPermissionAction): string
    {
        if ($roleId === null || $roleId === '') {
            throw new InvalidUserPermissionActionPayloadException($userPermissionAction->value, 'roleId');
        }

        return $roleId;
    }

    private function requirePermission(?string $permission, UserPermissionAction $userPermissionAction): string
    {
        if ($permission === null || $permission === '') {
            throw new InvalidUserPermissionActionPayloadException($userPermissionAction->value, 'permission');
        }

        return $permission;
    }

    private function requireOverrideType(?string $type): string
    {
        if ($type === null || $type === '') {
            throw new InvalidUserPermissionActionPayloadException(UserPermissionAction::SetOverride->value, 'overrideType');
        }

        return $type;
    }

    private function requireOverrideScope(?string $scope): string
    {
        if ($scope === null || $scope === '') {
            throw new InvalidUserPermissionActionPayloadException(UserPermissionAction::SetOverride->value, 'overrideScope');
        }

        return $scope;
    }
}
