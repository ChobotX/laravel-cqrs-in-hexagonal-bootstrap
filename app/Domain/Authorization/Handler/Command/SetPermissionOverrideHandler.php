<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Command\SetPermissionOverrideCommand;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Event\PermissionOverrideSet;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Enum\Action;
use App\Domain\Authorization\Enum\OverrideType;
use App\Domain\Authorization\ValueObject\Feature;
use App\Domain\Authorization\ValueObject\Module;
use App\Domain\Authorization\ValueObject\PermissionKey;
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
        $permissionKey = new PermissionKey(
            new Module($parts[PermissionKey::MODULE_INDEX]),
            isset($parts[PermissionKey::FEATURE_INDEX]) ? new Feature($parts[PermissionKey::FEATURE_INDEX]) : null,
            isset($parts[PermissionKey::ACTION_INDEX]) ? Action::from($parts[PermissionKey::ACTION_INDEX]) : null,
        );
        $overrideType = OverrideType::from($command->type);
        $accessScope = AccessScope::from($command->scope);

        $this->userPermissionRepository->setOverride(
            $command->userId,
            $permissionKey,
            $overrideType,
            $accessScope,
        );

        $this->eventCollector->collect(new PermissionOverrideSet(
            userId: $command->userId,
            permission: $command->permission,
            type: $command->type,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
