<?php

declare(strict_types=1);

use App\Domain\File\Contract\Command\StoreFileCommand;
use App\Domain\File\Contract\Event\FileStored;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\File\Handler\Command\StoreFileHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeFileRepository;
use Tests\Helper\FakeFileStorage;

it('stores a file and emits event', function (): void {
    $fileRepo = new FakeFileRepository;
    $fileStorage = new FakeFileStorage;
    $eventCollector = new FakeEventCollector;

    $handler = new StoreFileHandler($fileRepo, $fileStorage, $eventCollector);

    $upload = new FileUpload(
        originalName: new FileName('report.pdf'),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 1024,
        file: new SplFileInfo(__FILE__),
    );

    $handler->handle(new StoreFileCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'documents',
        uploadedBy: '660e8400-e29b-41d4-a716-446655440000',
        upload: $upload,
    ));

    expect($fileRepo->saved)->toHaveCount(1)
        ->and($fileRepo->saved[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($fileRepo->saved[0]->namespace->value)->toBe('documents')
        ->and($fileRepo->saved[0]->originalName->value)->toBe('report.pdf')
        ->and($fileRepo->saved[0]->mimeType->value)->toBe('application/pdf')
        ->and($fileRepo->saved[0]->sizeInBytes)->toBe(1024)
        ->and($fileRepo->saved[0]->version->value)->toBe(1)
        ->and($fileRepo->saved[0]->uploadedBy)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($fileStorage->stored)->toHaveCount(1)
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(FileStored::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof FileStored);

    expect($event->fileId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->version)->toBe(1);
});

it('increments version for same namespace and name', function (): void {
    $firstFile = new App\Domain\File\Contract\Entity\File(
        id: new App\Domain\File\Contract\ValueObject\FileId('550e8400-e29b-41d4-a716-446655440000'),
        namespace: new App\Domain\File\ValueObject\FileNamespace('documents'),
        originalName: new FileName('report.pdf'),
        storagePath: new App\Domain\File\ValueObject\StoragePath('documents/old.pdf'),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 512,
        version: new App\Domain\File\ValueObject\FileVersion(1),
        uploadedBy: '660e8400-e29b-41d4-a716-446655440000',
        uploadedAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
    );

    $fileRepo = new FakeFileRepository(['550e8400-e29b-41d4-a716-446655440000' => $firstFile]);
    $fileStorage = new FakeFileStorage;
    $eventCollector = new FakeEventCollector;

    $handler = new StoreFileHandler($fileRepo, $fileStorage, $eventCollector);

    $upload = new FileUpload(
        originalName: new FileName('report.pdf'),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 2048,
        file: new SplFileInfo(__FILE__),
    );

    $handler->handle(new StoreFileCommand(
        id: '770e8400-e29b-41d4-a716-446655440000',
        namespace: 'documents',
        uploadedBy: '660e8400-e29b-41d4-a716-446655440000',
        upload: $upload,
    ));

    $event = $eventCollector->collected[0];
    assert($event instanceof FileStored);

    expect($fileRepo->saved)->toHaveCount(1)
        ->and($fileRepo->saved[0]->version->value)->toBe(2)
        ->and($event->version)->toBe(2);

    $versions = $fileRepo->findVersions(
        new App\Domain\File\ValueObject\FileNamespace('documents'),
        new FileName('report.pdf'),
    );

    expect($versions)->toHaveCount(2)
        ->and($versions[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($versions[0]->version->value)->toBe(1)
        ->and($versions[1]->id->value)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($versions[1]->version->value)->toBe(2);
});
