<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Service;

use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\ValueObject\FileNamespace;
use App\Domain\File\ValueObject\StoragePath;

interface FileStorage
{
    public function store(FileNamespace $fileNamespace, FileUpload $fileUpload): StoragePath;

    public function retrieve(StoragePath $storagePath): string;

    public function delete(StoragePath $storagePath): void;

    public function exists(StoragePath $storagePath): bool;

    public function url(StoragePath $storagePath): string;
}
