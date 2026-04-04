<?php

declare(strict_types=1);

namespace App\Domain\File\Command\StoreAvatar;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\Image\ImageProcessor;
use App\Domain\File\Contract\Command\StoreAvatar\StoreAvatarCommand;
use App\Domain\File\Contract\Event\FileStored;
use App\Domain\File\Contract\File;
use App\Domain\File\Contract\FileId;
use App\Domain\File\Contract\FileName;
use App\Domain\File\Contract\FileRepository;
use App\Domain\File\Contract\FileStorage;
use App\Domain\File\Contract\FileUpload;
use App\Domain\File\Contract\MimeType;
use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use DateTimeImmutable;

/** @implements CommandHandler<StoreAvatarCommand> */
final readonly class StoreAvatarHandler implements CommandHandler
{
    private const int AVATAR_MAX_DIMENSION = 640;

    private const string OUTPUT_MIME = 'image/webp';

    private const string OUTPUT_EXTENSION = 'webp';

    public function __construct(
        private FileRepository $fileRepository,
        private FileStorage $fileStorage,
        private ImageProcessor $imageProcessor,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $fileNamespace = new FileNamespace($command->namespace);
        $processed = $this->imageProcessor->resizeAndConvertToWebp($command->upload->file, self::AVATAR_MAX_DIMENSION);

        $fileUpload = new FileUpload(
            originalName: new FileName(pathinfo($command->upload->originalName->value, PATHINFO_FILENAME).'.'.self::OUTPUT_EXTENSION),
            mimeType: new MimeType(self::OUTPUT_MIME),
            sizeInBytes: (int) $processed->getSize(),
            file: $processed,
        );

        $version = $this->fileRepository->nextVersionNumber($fileNamespace, $fileUpload->originalName);
        $storagePath = $this->fileStorage->store($fileNamespace, $fileUpload);

        $now = new DateTimeImmutable;

        $file = new File(
            id: new FileId($command->id),
            namespace: $fileNamespace,
            originalName: $fileUpload->originalName,
            storagePath: $storagePath,
            mimeType: $fileUpload->mimeType,
            sizeInBytes: $fileUpload->sizeInBytes,
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
