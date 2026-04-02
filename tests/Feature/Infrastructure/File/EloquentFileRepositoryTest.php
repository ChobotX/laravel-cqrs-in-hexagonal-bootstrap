<?php

declare(strict_types=1);

use App\Domain\File\Contract\FileId;
use App\Domain\File\File;
use App\Domain\File\FileName;
use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use App\Domain\File\MimeType;
use App\Domain\File\StoragePath;
use App\Infrastructure\Eloquent\File\EloquentFileRepository;
use App\Infrastructure\Eloquent\File\FileMapper;
use App\Infrastructure\Eloquent\User\UserModel;

function testUserId(): string
{
    $userId = '550e8400-e29b-41d4-a716-446655440e00';

    if (UserModel::find($userId) === null) {
        UserModel::create(['id' => $userId, 'name' => 'Test User', 'email' => 'file-test@test.com']);
    }

    return $userId;
}

function fileRepo(): EloquentFileRepository
{
    return new EloquentFileRepository(new FileMapper);
}

function makeTestFile(string $id, string $namespace, string $name, string $path, int $version, string $uploadedBy): File
{
    return new File(
        id: new FileId($id),
        namespace: new FileNamespace($namespace),
        originalName: new FileName($name),
        storagePath: new StoragePath($path),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 1024,
        version: new FileVersion($version),
        uploadedBy: $uploadedBy,
        uploadedAt: new DateTimeImmutable('2025-01-15T10:00:00+00:00'),
    );
}

it('creates and finds a file by id', function (): void {
    $eloquentFileRepository = fileRepo();
    $userId = testUserId();
    $file = makeTestFile('550e8400-e29b-41d4-a716-446655440f00', 'documents', 'report.pdf', 'documents/abc.pdf', 1, $userId);

    $eloquentFileRepository->create($file);
    $found = $eloquentFileRepository->findById($file->id);

    expect($found)->not->toBeNull()
        ->and($found->originalName->value)->toBe('report.pdf')
        ->and($found->namespace->value)->toBe('documents')
        ->and($found->storagePath->value)->toBe('documents/abc.pdf')
        ->and($found->mimeType->value)->toBe('application/pdf')
        ->and($found->sizeInBytes)->toBe(1024)
        ->and($found->version->value)->toBe(1)
        ->and($found->uploadedBy)->toBe($userId)
        ->and($found->uploadedAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('returns null for non-existent id', function (): void {
    $found = fileRepo()->findById(new FileId('550e8400-e29b-41d4-a716-446655440f99'));

    expect($found)->toBeNull();
});

it('soft-deletes a file', function (): void {
    $eloquentFileRepository = fileRepo();
    $userId = testUserId();
    $file = makeTestFile('550e8400-e29b-41d4-a716-446655440f01', 'documents', 'to-delete.pdf', 'documents/del.pdf', 1, $userId);

    $eloquentFileRepository->create($file);
    $eloquentFileRepository->delete($file->id);

    $found = $eloquentFileRepository->findById($file->id);

    expect($found)->toBeNull();
});

it('delete does nothing for non-existent id', function (): void {
    $eloquentFileRepository = fileRepo();

    $eloquentFileRepository->delete(new FileId('550e8400-e29b-41d4-a716-446655440f99'));

    expect(true)->toBeTrue();
});

it('finds versions ordered by version number', function (): void {
    $eloquentFileRepository = fileRepo();
    $userId = testUserId();

    $eloquentFileRepository->create(makeTestFile('550e8400-e29b-41d4-a716-446655440f02', 'documents', 'report.pdf', 'documents/v1.pdf', 1, $userId));
    $eloquentFileRepository->create(makeTestFile('550e8400-e29b-41d4-a716-446655440f03', 'documents', 'report.pdf', 'documents/v2.pdf', 2, $userId));

    $versions = $eloquentFileRepository->findVersions(new FileNamespace('documents'), new FileName('report.pdf'));

    expect($versions)->toHaveCount(2)
        ->and($versions[0]->version->value)->toBe(1)
        ->and($versions[1]->version->value)->toBe(2);
});

it('returns empty versions for unknown file', function (): void {
    $versions = fileRepo()->findVersions(new FileNamespace('documents'), new FileName('nonexistent.pdf'));

    expect($versions)->toBeEmpty();
});

it('finds latest version', function (): void {
    $eloquentFileRepository = fileRepo();
    $userId = testUserId();

    $eloquentFileRepository->create(makeTestFile('550e8400-e29b-41d4-a716-446655440f04', 'avatars', 'photo.jpg', 'avatars/v1.jpg', 1, $userId));
    $eloquentFileRepository->create(makeTestFile('550e8400-e29b-41d4-a716-446655440f05', 'avatars', 'photo.jpg', 'avatars/v2.jpg', 2, $userId));

    $latest = $eloquentFileRepository->latestVersion(new FileNamespace('avatars'), new FileName('photo.jpg'));

    expect($latest)->not->toBeNull()
        ->and($latest->version->value)->toBe(2);
});

it('returns null for latest version when none exist', function (): void {
    $latest = fileRepo()->latestVersion(new FileNamespace('documents'), new FileName('nonexistent.pdf'));

    expect($latest)->toBeNull();
});

it('computes next version number', function (): void {
    $eloquentFileRepository = fileRepo();
    $userId = testUserId();

    $next = $eloquentFileRepository->nextVersionNumber(new FileNamespace('documents'), new FileName('new.pdf'));

    expect($next)->toBe(1);

    $eloquentFileRepository->create(makeTestFile('550e8400-e29b-41d4-a716-446655440f06', 'documents', 'new.pdf', 'documents/v1.pdf', 1, $userId));

    $next = $eloquentFileRepository->nextVersionNumber(new FileNamespace('documents'), new FileName('new.pdf'));

    expect($next)->toBe(2);
});
