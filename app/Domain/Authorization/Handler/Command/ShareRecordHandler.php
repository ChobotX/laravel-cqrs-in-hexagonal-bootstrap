<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Command\ShareRecordCommand;
use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\Event\RecordShared;
use App\Domain\Authorization\Contract\Repository\RecordShareRepository;
use App\Domain\Authorization\Contract\ValueObject\RecordShare;
use DateTimeImmutable;

/** @implements CommandHandler<ShareRecordCommand> */
final readonly class ShareRecordHandler implements CommandHandler
{
    public function __construct(
        private RecordShareRepository $recordShareRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $now = new DateTimeImmutable;

        foreach ($command->actions as $actionValue) {
            $action = Action::from($actionValue);

            $this->recordShareRepository->share(new RecordShare(
                granteeUserId: $command->granteeUserId,
                resourceType: $command->resourceType,
                resourceId: $command->resourceId,
                action: $action,
                grantorUserId: $command->grantorUserId,
            ));

            $this->eventCollector->collect(new RecordShared(
                granteeUserId: $command->granteeUserId,
                resourceType: $command->resourceType,
                resourceId: $command->resourceId,
                action: $action->value,
                grantorUserId: $command->grantorUserId,
                occurredAt: $now,
            ));
        }
    }
}
