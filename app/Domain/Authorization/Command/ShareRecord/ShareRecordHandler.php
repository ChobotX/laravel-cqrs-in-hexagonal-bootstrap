<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\ShareRecord;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Contract\Command\ShareRecord\ShareRecordCommand;
use App\Domain\Authorization\Contract\Event\RecordShared;
use App\Domain\Authorization\Contract\RecordShareRepository;
use App\Domain\Authorization\RecordShare;
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
        $recordShare = new RecordShare(
            granteeUserId: $command->granteeUserId,
            resourceType: $command->resourceType,
            resourceId: $command->resourceId,
            action: Action::from($command->action),
            grantorUserId: $command->grantorUserId,
        );

        $this->recordShareRepository->share($recordShare);

        $this->eventCollector->collect(new RecordShared(
            granteeUserId: $command->granteeUserId,
            resourceType: $command->resourceType,
            resourceId: $command->resourceId,
            action: $command->action,
            grantorUserId: $command->grantorUserId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
