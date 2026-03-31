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

        $this->userPermissionRepository->removeOverride(
            $command->userId,
            new PermissionKey(
                new Module($parts[PermissionKey::MODULE_INDEX]),
                isset($parts[PermissionKey::FEATURE_INDEX]) ? new Feature($parts[PermissionKey::FEATURE_INDEX]) : null,
                isset($parts[PermissionKey::ACTION_INDEX]) ? Action::from($parts[PermissionKey::ACTION_INDEX]) : null,
            ),
        );

        $this->eventCollector->collect(new PermissionOverrideRemoved(
            userId: $command->userId,
            permission: $command->permission,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
