<?php

declare(strict_types=1);

use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\Contract\ValueObject\MimeType;
use App\Domain\File\Exception\FileStorageException;
use App\Domain\File\ValueObject\FileNamespace;
use App\Domain\File\ValueObject\StoragePath;
use App\Infrastructure\Filesystem\LaravelFileStorage;
use Illuminate\Support\Facades\Storage;

it('stores a file and returns a storage path', function (): void {
    Storage::fake('files');

    $storage = new LaravelFileStorage(Storage::disk('files'));
    $namespace = new FileNamespace('documents');

    $tempPath = tempnam(sys_get_temp_dir(), 'file-test');
    file_put_contents($tempPath, 'test-content');

    $upload = new FileUpload(
        originalName: new FileName('report.pdf'),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 12,
        file: new SplFileInfo($tempPath),
    );

    $storagePath = $storage->store($namespace, $upload);

    expect($storagePath)->toBeInstanceOf(StoragePath::class)
        ->and($storagePath->value)->toStartWith('documents/')
        ->and($storagePath->value)->toEndWith('.pdf');

    Storage::disk('files')->assertExists($storagePath->value);

    unlink($tempPath);
});

it('stores a file without extension', function (): void {
    Storage::fake('files');

    $storage = new LaravelFileStorage(Storage::disk('files'));
    $namespace = new FileNamespace('binaries');

    $tempPath = tempnam(sys_get_temp_dir(), 'file-test');
    file_put_contents($tempPath, 'binary-content');

    $upload = new FileUpload(
        originalName: new FileName('no-extension'),
        mimeType: new MimeType('application/octet-stream'),
        sizeInBytes: 14,
        file: new SplFileInfo($tempPath),
    );

    $storagePath = $storage->store($namespace, $upload);

    expect($storagePath->value)->toStartWith('binaries/')
        ->and($storagePath->value)->not->toContain('.');

    Storage::disk('files')->assertExists($storagePath->value);

    unlink($tempPath);
});

it('retrieves stored file contents', function (): void {
    Storage::fake('files');

    Storage::disk('files')->put('documents/test.pdf', 'pdf-content');

    $storage = new LaravelFileStorage(Storage::disk('files'));
    $contents = $storage->retrieve(new StoragePath('documents/test.pdf'));

    expect($contents)->toBe('pdf-content');
});

it('throws on retrieve when file does not exist', function (): void {
    Storage::fake('files');

    $storage = new LaravelFileStorage(Storage::disk('files'));

    $storage->retrieve(new StoragePath('documents/nonexistent.pdf'));
})->throws(FileStorageException::class);

it('deletes a file', function (): void {
    Storage::fake('files');

    Storage::disk('files')->put('documents/to-delete.pdf', 'content');

    $storage = new LaravelFileStorage(Storage::disk('files'));
    $storage->delete(new StoragePath('documents/to-delete.pdf'));

    Storage::disk('files')->assertMissing('documents/to-delete.pdf');
});

it('checks file existence', function (): void {
    Storage::fake('files');

    Storage::disk('files')->put('documents/exists.pdf', 'content');

    $storage = new LaravelFileStorage(Storage::disk('files'));

    expect($storage->exists(new StoragePath('documents/exists.pdf')))->toBeTrue()
        ->and($storage->exists(new StoragePath('documents/missing.pdf')))->toBeFalse();
});

it('throws FileStorageException when source file cannot be opened', function (): void {
    Storage::fake('files');

    $storage = new LaravelFileStorage(Storage::disk('files'));

    $upload = new FileUpload(
        originalName: new FileName('ghost.pdf'),
        mimeType: new MimeType('application/pdf'),
        sizeInBytes: 0,
        file: new SplFileInfo('/nonexistent/path/to/file.pdf'),
    );

    $storage->store(new FileNamespace('documents'), $upload);
})->throws(FileStorageException::class);

it('generates url for a file', function (): void {
    Storage::fake('files');

    $storage = new LaravelFileStorage(Storage::disk('files'));
    $url = $storage->url(new StoragePath('documents/file.pdf'));

    expect($url)->toContain('documents/file.pdf');
});
