<?php

declare(strict_types=1);

namespace App\Domain\File\Command\StoreFile;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\File\Contract\Command\StoreFile\StoreFileCommand;
use App\Domain\File\Contract\Event\FileStored;
use App\Domain\File\Contract\File;
use App\Domain\File\Contract\FileId;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\Contract\FileStorage;
use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use DateTimeImmutable;

/** @implements CommandHandler<StoreFileCommand> */
final readonly class StoreFileHandler implements CommandHandler
{
    public function __construct(
        private FileRepository $fileRepository,
        private FileStorage $fileStorage,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $fileNamespace = new FileNamespace($command->namespace);
        $version = $this->fileRepository->nextVersionNumber($fileNamespace, $command->upload->originalName);
        $storagePath = $this->fileStorage->store($fileNamespace, $command->upload);

        $now = new DateTimeImmutable;

        $file = new File(
            id: new FileId($command->id),
            namespace: $fileNamespace,
            originalName: $command->upload->originalName,
            storagePath: $storagePath,
            mimeType: $command->upload->mimeType,
            sizeInBytes: $command->upload->sizeInBytes,
            version: new FileVersion($version),
            uploadedBy: $command->uploadedBy,
            uploadedAt: $now,
        );

        $this->fileRepository->create($file);

        $this->eventCollector->collect(new FileStored(
            fileId: $file->id->value,
            namespace: $fileNamespace->value,
            originalName: $file->originalName->value,
            storagePath: $file->storagePath->value,
            version: $version,
            uploadedBy: $command->uploadedBy,
            occurredAt: $now,
        ));
    }
}
