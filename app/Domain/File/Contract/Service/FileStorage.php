<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Service;

use App\Domain\File\Contract\ValueObject\FileUpload;
use App\Domain\File\ValueObject\FileNamespace;
use App\Domain\File\ValueObject\StoragePath;

/**
 * Domain service contract for file storage in the File bounded context.
 */
interface FileStorage
{
    /** Persists a new or updated aggregate row. */
    public function store(FileNamespace $fileNamespace, FileUpload $fileUpload): StoragePath;

    /** Contract operation `retrieve`; see infrastructure for behavior. */
    public function retrieve(StoragePath $storagePath): string;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(StoragePath $storagePath): void;

    /** Whether the targeted resource is present. */
    public function exists(StoragePath $storagePath): bool;

    /** Builds a public URL for the stored asset. */
    public function url(StoragePath $storagePath): string;
}
