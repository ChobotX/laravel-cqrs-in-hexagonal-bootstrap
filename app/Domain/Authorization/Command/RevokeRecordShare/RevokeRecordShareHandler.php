<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\RevokeRecordShare;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Event\RecordShareRevoked;
use App\Domain\Authorization\Exception\RecordShareNotFoundException;
use App\Domain\Authorization\RecordShareRepository;
use DateTimeImmutable;

/** @implements CommandHandler<RevokeRecordShareCommand> */
final readonly class RevokeRecordShareHandler implements CommandHandler
{
    public function __construct(
        private RecordShareRepository $recordShareRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        if (! $this->recordShareRepository->exists($command->granteeUserId, $command->resourceType, $command->resourceId)) {
            throw new RecordShareNotFoundException($command->granteeUserId, $command->resourceType, $command->resourceId);
        }

        $this->recordShareRepository->revoke(
            $command->granteeUserId,
            $command->resourceType,
            $command->resourceId,
        );

        $this->eventCollector->collect(new RecordShareRevoked(
            granteeUserId: $command->granteeUserId,
            resourceType: $command->resourceType,
            resourceId: $command->resourceId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
