<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityDeleted;
use App\Domain\File\Contract\Event\FileDeleted;
use App\Domain\File\Contract\Event\FileStored;

it('FileStored implements DomainEvent and exposes properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new FileStored(
        fileId: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'documents',
        originalName: 'report.pdf',
        storagePath: 'documents/abc-123.pdf',
        version: 1,
        uploadedBy: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->fileId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->namespace)->toBe('documents')
        ->and($event->originalName)->toBe('report.pdf')
        ->and($event->storagePath)->toBe('documents/abc-123.pdf')
        ->and($event->version)->toBe(1)
        ->and($event->uploadedBy)->toBe('660e8400-e29b-41d4-a716-446655440000');
});

it('FileDeleted implements DomainEvent and EntityDeleted', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new FileDeleted(
        fileId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event)->toBeInstanceOf(EntityDeleted::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->entityId())->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->entityType())->toBe('file')
        ->and($event->fileId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
