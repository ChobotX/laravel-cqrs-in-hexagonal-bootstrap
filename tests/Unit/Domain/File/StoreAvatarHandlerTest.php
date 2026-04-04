<?php

declare(strict_types=1);

use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\Event\FileStored;
use App\Domain\File\Contract\Service\ImageProcessor;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\File\Handler\Command\StoreAvatarHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeFileRepository;
use Tests\Helper\FakeFileStorage;

it('processes image and stores as webp', function (): void {
    $fileRepo = new FakeFileRepository;
    $fileStorage = new FakeFileStorage;
    $eventCollector = new FakeEventCollector;

    $processedFile = new SplFileInfo(__FILE__);
    $imageProcessor = new class($processedFile) implements ImageProcessor
    {
        public bool $called = false;

        public function __construct(private readonly SplFileInfo $output) {}

        public function resizeAndConvertToWebp(SplFileInfo $source, int $maxDimension): SplFileInfo
        {
            $this->called = true;

            return $this->output;
        }
    };

    $handler = new StoreAvatarHandler($fileRepo, $fileStorage, $imageProcessor, $eventCollector);

    $upload = new FileUpload(
        originalName: new FileName('photo.jpg'),
        mimeType: new MimeType('image/jpeg'),
        sizeInBytes: 5000,
        file: new SplFileInfo(__FILE__),
    );

    $handler->handle(new StoreAvatarCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'user-avatars',
        uploadedBy: '660e8400-e29b-41d4-a716-446655440000',
        upload: $upload,
    ));

    expect($imageProcessor->called)->toBeTrue()
        ->and($fileRepo->saved)->toHaveCount(1)
        ->and($fileRepo->saved[0]->mimeType->value)->toBe('image/webp')
        ->and($fileRepo->saved[0]->originalName->value)->toBe('photo.webp')
        ->and($fileStorage->stored)->toHaveCount(1)
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(FileStored::class);
});
