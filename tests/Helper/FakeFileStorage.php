<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\File\Contract\FileStorage;
use App\Domain\File\Contract\FileUpload;
use App\Domain\File\Exception\FileStorageException;
use App\Domain\File\FileNamespace;
use App\Domain\File\StoragePath;

final class FakeFileStorage implements FileStorage
{
    /** @var array<string, string> */
    public array $stored = [];

    /** @var list<string> */
    public array $deletedPaths = [];

    private int $storeCallCount = 0;

    public function store(FileNamespace $fileNamespace, FileUpload $fileUpload): StoragePath
    {
        $this->storeCallCount++;
        $extension = pathinfo($fileUpload->originalName->value, PATHINFO_EXTENSION);
        $path = $extension !== ''
            ? sprintf('%s/fake-%d.%s', $fileNamespace->value, $this->storeCallCount, $extension)
            : sprintf('%s/fake-%d', $fileNamespace->value, $this->storeCallCount);

        $this->stored[$path] = 'fake-content';

        return new StoragePath($path);
    }

    public function retrieve(StoragePath $storagePath): string
    {
        if (! isset($this->stored[$storagePath->value])) {
            throw new FileStorageException($storagePath->value);
        }

        return $this->stored[$storagePath->value];
    }

    public function delete(StoragePath $storagePath): void
    {
        $this->deletedPaths[] = $storagePath->value;
        unset($this->stored[$storagePath->value]);
    }

    public function exists(StoragePath $storagePath): bool
    {
        return isset($this->stored[$storagePath->value]);
    }

    public function url(StoragePath $storagePath): string
    {
        return sprintf('https://storage.example.com/%s', $storagePath->value);
    }
}
