<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Command\StartImpersonationCommand;
use App\Domain\Authorization\Contract\Event\ImpersonationStarted;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\Service\ImpersonationManager;
use App\Domain\Authorization\Exception\ImpersonationNotAllowedException;
use DateTimeImmutable;

/** @implements CommandHandler<StartImpersonationCommand> */
final readonly class StartImpersonationHandler implements CommandHandler
{
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
        private ImpersonationManager $impersonationManager,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        if (! $this->isSuperAdmin($command->impersonatorId)) {
            throw new ImpersonationNotAllowedException($command->impersonatorId);
        }

        $this->impersonationManager->start($command->impersonatorId, $command->targetUserId);

        $this->eventCollector->collect(new ImpersonationStarted(
            impersonatorId: $command->impersonatorId,
            targetUserId: $command->targetUserId,
            occurredAt: new DateTimeImmutable,
        ));
    }

    private function isSuperAdmin(string $userId): bool
    {
        $roles = $this->userPermissionRepository->userRoles($userId);

        return array_any($roles, fn ($role) => $role->isSystem);
    }
}
