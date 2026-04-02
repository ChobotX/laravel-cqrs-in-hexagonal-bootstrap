<?php

declare(strict_types=1);

use App\Domain\File\Contract\FileId;
use App\Domain\File\Exception\FileNotFoundException;
use App\Domain\File\File;
use App\Domain\File\FileName;
use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use App\Domain\File\MimeType;
use App\Domain\File\Query\GetFileContent\GetFileContentHandler;
use App\Domain\File\Query\GetFileContent\GetFileContentQuery;
use App\Domain\File\StoragePath;
use Tests\Helper\FakeFileRepository;
use Tests\Helper\FakeFileStorage;

it('returns file content', function (): void {
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
    $fileStorage->stored['documents/abc-123.pdf'] = 'pdf-content-bytes';

    $handler = new GetFileContentHandler($fileRepo, $fileStorage);

    $result = $handler->handle(new GetFileContentQuery('550e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toBe('pdf-content-bytes');
});

it('throws when file not found', function (): void {
    $fileRepo = new FakeFileRepository;
    $fileStorage = new FakeFileStorage;

    $handler = new GetFileContentHandler($fileRepo, $fileStorage);

    $handler->handle(new GetFileContentQuery('550e8400-e29b-41d4-a716-446655440000'));
})->throws(FileNotFoundException::class);
