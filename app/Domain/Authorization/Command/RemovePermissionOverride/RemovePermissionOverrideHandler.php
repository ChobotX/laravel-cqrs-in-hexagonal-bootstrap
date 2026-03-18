<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\RemovePermissionOverride;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\UserPermissionRepository;
use DateTimeImmutable;

/** @implements CommandHandler<RemovePermissionOverrideCommand> */
final readonly class RemovePermissionOverrideHandler implements CommandHandler
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

        $this->userPermissionRepository->removeOverride(
            $command->userId,
            $command->organizationId,
            new PermissionKey($module, $feature, $action),
        );

        $this->eventCollector->collect(new PermissionOverrideRemoved(
            userId: $command->userId,
            organizationId: $command->organizationId,
            permission: $command->permission,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
