<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\SetPermissionOverride;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Event\PermissionOverrideSet;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\UserPermissionRepository;
use DateTimeImmutable;

/** @implements CommandHandler<SetPermissionOverrideCommand> */
final readonly class SetPermissionOverrideHandler implements CommandHandler
{
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $parts = explode('.', $command->permission);
        $module = new Module($parts[0]);
        $feature = isset($parts[1]) ? new Feature($parts[1]) : null;
        $action = isset($parts[2]) ? Action::from($parts[2]) : null;

        $permissionKey = new PermissionKey($module, $feature, $action);
        $overrideType = OverrideType::from($command->type);
        $accessScope = AccessScope::from($command->scope);

        $this->userPermissionRepository->setOverride(
            $command->userId,
            $command->organizationId,
            $permissionKey,
            $overrideType,
            $accessScope,
        );

        $this->eventCollector->collect(new PermissionOverrideSet(
            userId: $command->userId,
            organizationId: $command->organizationId,
            permission: $command->permission,
            type: $command->type,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
