<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Registry\Contract\Command\DeleteEntryCommand;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Contract\Event\EntryDeleted;
use App\Domain\Registry\Exception\EntryNotFoundException;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteEntryCommand> */
final readonly class DeleteEntryHandler implements CommandHandler
{
    public function __construct(
        private EntryRepository $entryRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $entry = $this->entryRepository->findById(new EntryId($command->id))
            ?? throw new EntryNotFoundException($command->id);

        $this->entryRepository->delete($entry->id);

        $this->eventCollector->collect(new EntryDeleted(
            entryId: $entry->id->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
