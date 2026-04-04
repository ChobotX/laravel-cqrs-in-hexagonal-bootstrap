<?php

declare(strict_types=1);

use App\Domain\File\Contract\Command\DeleteFileCommand;
use App\Domain\File\Contract\Entity\File;
use App\Domain\File\Contract\Event\FileDeleted;
use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\File\Exception\FileNotFoundException;
use App\Domain\File\Handler\Command\DeleteFileHandler;
use App\Domain\File\ValueObject\FileNamespace;
use App\Domain\File\ValueObject\FileVersion;
use App\Domain\File\ValueObject\StoragePath;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeFileRepository;
use Tests\Helper\FakeFileStorage;

it('deletes a file and emits event', function (): void {
    $file = new File(
        id: new FileId('550e8400-e29b-41d4-a716-446655440000'),
        namespace: new FileNamespace('documents'),
        originalName: new FileName('report.pdf'),
        storagePath: new StoragePath('documents/abc-123.pdf'),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 1024,
        version: new FileVersion(1),
        uploadedBy: '660e8400-e29b-41d4-a716-446655440000',
        uploadedAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
    );

    $fileRepo = new FakeFileRepository(['550e8400-e29b-41d4-a716-446655440000' => $file]);
    $fileStorage = new FakeFileStorage;
    $fileStorage->stored['documents/abc-123.pdf'] = 'content';
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteFileHandler($fileRepo, $fileStorage, $eventCollector);

    $handler->handle(new DeleteFileCommand('550e8400-e29b-41d4-a716-446655440000'));

    expect($fileRepo->deleted)->toHaveCount(1)
        ->and($fileRepo->deleted[0])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($fileStorage->deletedPaths)->toHaveCount(1)
        ->and($fileStorage->deletedPaths[0])->toBe('documents/abc-123.pdf')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(FileDeleted::class);
});

it('throws when file not found', function (): void {
    $fileRepo = new FakeFileRepository;
    $fileStorage = new FakeFileStorage;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteFileHandler($fileRepo, $fileStorage, $eventCollector);

    $handler->handle(new DeleteFileCommand('550e8400-e29b-41d4-a716-446655440000'));
})->throws(FileNotFoundException::class);
