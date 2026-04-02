<?php

declare(strict_types=1);

use App\Domain\File\Contract\FileId;
use App\Domain\File\File;
use App\Domain\File\FileName;
use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use App\Domain\File\MimeType;
use App\Domain\File\Query\GetFileVersions\GetFileVersionsHandler;
use App\Domain\File\Query\GetFileVersions\GetFileVersionsQuery;
use App\Domain\File\StoragePath;
use Tests\Helper\FakeFileRepository;

it('returns versions ordered by version number', function (): void {
    $v1 = new File(
        id: new FileId('550e8400-e29b-41d4-a716-446655440000'),
        namespace: new FileNamespace('documents'),
        originalName: new FileName('report.pdf'),
        storagePath: new StoragePath('documents/v1.pdf'),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 512,
        version: new FileVersion(1),
        uploadedBy: '660e8400-e29b-41d4-a716-446655440000',
        uploadedAt: new DateTimeImmutable('2025-01-01T00:00:00+00:00'),
    );

    $v2 = new File(
        id: new FileId('770e8400-e29b-41d4-a716-446655440000'),
        namespace: new FileNamespace('documents'),
        originalName: new FileName('report.pdf'),
        storagePath: new StoragePath('documents/v2.pdf'),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 1024,
        version: new FileVersion(2),
        uploadedBy: '660e8400-e29b-41d4-a716-446655440000',
        uploadedAt: new DateTimeImmutable('2025-01-02T00:00:00+00:00'),
    );

    $fileRepo = new FakeFileRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $v1,
        '770e8400-e29b-41d4-a716-446655440000' => $v2,
    ]);

    $handler = new GetFileVersionsHandler($fileRepo);

    $result = $handler->handle(new GetFileVersionsQuery('documents', 'report.pdf'));

    expect($result)->toHaveCount(2)
        ->and($result[0]->version->value)->toBe(1)
        ->and($result[1]->version->value)->toBe(2);
});

it('returns empty list when no versions exist', function (): void {
    $fileRepo = new FakeFileRepository;
    $handler = new GetFileVersionsHandler($fileRepo);

    $result = $handler->handle(new GetFileVersionsQuery('documents', 'nonexistent.pdf'));

    expect($result)->toBeEmpty();
});
