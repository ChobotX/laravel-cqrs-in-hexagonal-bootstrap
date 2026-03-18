<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\StopImpersonation;

use App\Contract\Authorization\ImpersonationManager;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Event\ImpersonationStopped;
use DateTimeImmutable;

/** @implements CommandHandler<StopImpersonationCommand> */
final readonly class StopImpersonationHandler implements CommandHandler
{
    public function __construct(
        private ImpersonationManager $impersonationManager,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $targetUserId = $this->impersonationManager->impersonatedUserId() ?? '';

        $this->impersonationManager->stop();

        $this->eventCollector->collect(new ImpersonationStopped(
            impersonatorId: $command->impersonatorId,
            targetUserId: $targetUserId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
