<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\File\FileMapper;
use App\Infrastructure\Eloquent\File\FileModel;
use Tests\TestCase;

uses(TestCase::class);

it('maps a file model to domain', function (): void {
    $model = new FileModel;
    $model->id = '550e8400-e29b-41d4-a716-446655440000';
    $model->namespace = 'documents';
    $model->original_name = 'report.pdf';
    $model->storage_path = 'documents/abc-123.pdf';
    $model->mime_type = 'application/pdf';
    $model->size_in_bytes = 1024;
    $model->version_number = 1;
    $model->uploaded_by = '660e8400-e29b-41d4-a716-446655440000';
    $model->uploaded_at = new DateTimeImmutable('2025-01-15T10:00:00+00:00');

    $mapper = new FileMapper;
    $file = $mapper->toDomain($model);

    expect($file->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($file->namespace->value)->toBe('documents')
        ->and($file->originalName->value)->toBe('report.pdf')
        ->and($file->storagePath->value)->toBe('documents/abc-123.pdf')
        ->and($file->mimeType->value)->toBe('application/pdf')
        ->and($file->sizeInBytes)->toBe(1024)
        ->and($file->version->value)->toBe(1)
        ->and($file->uploadedBy)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($file->uploadedAt)->toBeInstanceOf(DateTimeImmutable::class);
});
