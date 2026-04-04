<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

use App\Domain\File\Contract\FileStorage;
use App\Domain\File\Contract\FileUpload;
use App\Domain\File\Exception\FileStorageException;
use App\Domain\File\FileNamespace;
use App\Domain\File\StoragePath;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

final readonly class LaravelFileStorage implements FileStorage
{
    public function __construct(
        private Filesystem $filesystem,
    ) {}

    public function store(FileNamespace $fileNamespace, FileUpload $fileUpload): StoragePath
    {
        $extension = pathinfo($fileUpload->originalName->value, PATHINFO_EXTENSION);
        $uuid = Str::uuid()->toString();

        $filename = $extension !== ''
            ? sprintf('%s.%s', $uuid, $extension)
            : $uuid;

        try {
            /** @var string $result */
            $result = $this->filesystem->putFileAs(
                $fileNamespace->value,
                new File($fileUpload->file->getPathname()),
                $filename,
            );
        } catch (FileNotFoundException) {
            throw new FileStorageException(sprintf('%s/%s', $fileNamespace->value, $filename));
        }

        return new StoragePath($result);
    }

    public function retrieve(StoragePath $storagePath): string
    {
        try {
            /** @var string $contents */
            $contents = $this->filesystem->get($storagePath->value);
        } catch (FilesystemException) {
            throw new FileStorageException($storagePath->value);
        }

        return $contents;
    }

    public function delete(StoragePath $storagePath): void
    {
        $this->filesystem->delete($storagePath->value);
    }

    public function exists(StoragePath $storagePath): bool
    {
        return $this->filesystem->exists($storagePath->value);
    }

    public function url(StoragePath $storagePath): string
    {
        return $this->filesystem->url($storagePath->value);
    }
}
