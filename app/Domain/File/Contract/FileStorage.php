<?php

declare(strict_types=1);

namespace App\Domain\File\Contract;

use App\Domain\File\FileNamespace;
use App\Domain\File\FileUpload;
use App\Domain\File\StoragePath;

interface FileStorage
{
    public function store(FileNamespace $fileNamespace, FileUpload $fileUpload): StoragePath;

    public function retrieve(StoragePath $storagePath): string;

    public function delete(StoragePath $storagePath): void;

    public function exists(StoragePath $storagePath): bool;

    public function url(StoragePath $storagePath): string;
}
