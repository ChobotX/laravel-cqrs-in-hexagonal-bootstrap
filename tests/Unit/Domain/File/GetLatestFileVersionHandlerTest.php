<?php

declare(strict_types=1);

use App\Domain\File\Contract\FileId;
use App\Domain\File\Exception\FileNotFoundException;
use App\Domain\File\File;
use App\Domain\File\FileName;
use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use App\Domain\File\MimeType;
use App\Domain\File\Query\GetLatestFileVersion\GetLatestFileVersionHandler;
use App\Domain\File\Query\GetLatestFileVersion\GetLatestFileVersionQuery;
use App\Domain\File\StoragePath;
use Tests\Helper\FakeFileRepository;

it('returns the latest version', function (): void {
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

    $handler = new GetLatestFileVersionHandler($fileRepo);

    $result = $handler->handle(new GetLatestFileVersionQuery('documents', 'report.pdf'));

    expect($result->version->value)->toBe(2)
        ->and($result->id->value)->toBe('770e8400-e29b-41d4-a716-446655440000');
});

it('throws when no versions exist', function (): void {
    $fileRepo = new FakeFileRepository;
    $handler = new GetLatestFileVersionHandler($fileRepo);

    $handler->handle(new GetLatestFileVersionQuery('documents', 'nonexistent.pdf'));
})->throws(FileNotFoundException::class);
