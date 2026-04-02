<?php

declare(strict_types=1);

use App\Domain\File\Contract\FileId;
use App\Domain\File\Exception\FileNotFoundException;
use App\Domain\File\File;
use App\Domain\File\FileName;
use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use App\Domain\File\MimeType;
use App\Domain\File\Query\GetFileById\GetFileByIdHandler;
use App\Domain\File\Query\GetFileById\GetFileByIdQuery;
use App\Domain\File\StoragePath;
use Tests\Helper\FakeFileRepository;

it('returns a file by id', function (): void {
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
    $handler = new GetFileByIdHandler($fileRepo);

    $result = $handler->handle(new GetFileByIdQuery('550e8400-e29b-41d4-a716-446655440000'));

    expect($result->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($result->originalName->value)->toBe('report.pdf');
});

it('throws when file not found', function (): void {
    $fileRepo = new FakeFileRepository;
    $handler = new GetFileByIdHandler($fileRepo);

    $handler->handle(new GetFileByIdQuery('550e8400-e29b-41d4-a716-446655440000'));
})->throws(FileNotFoundException::class);
