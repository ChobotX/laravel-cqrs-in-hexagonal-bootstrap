<?php

declare(strict_types=1);

namespace App\Domain\File\Command\DeleteFile;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\File\Contract\Event\FileDeleted;
use App\Domain\File\Contract\FileId;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\Contract\FileStorage;
use App\Domain\File\Exception\FileNotFoundException;
use App\Domain\File\File;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteFileCommand> */
final readonly class DeleteFileHandler implements CommandHandler
{
    public function __construct(
        private FileRepository $fileRepository,
        private FileStorage $fileStorage,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $fileId = new FileId($command->id);
        $existing = $this->fileRepository->findById($fileId);

        if (! $existing instanceof File) {
            throw new FileNotFoundException($command->id);
        }

        $this->fileStorage->delete($existing->storagePath);
        $this->fileRepository->delete($fileId);

        $this->eventCollector->collect(new FileDeleted(
            fileId: $fileId->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
