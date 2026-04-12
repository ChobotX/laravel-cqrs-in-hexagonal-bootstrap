<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Repository;

use App\Domain\File\Contract\Entity\File;
use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\ValueObject\FileNamespace;

/**
 * Persistence port for file data in the File context; implementations live in Infrastructure.
 */
interface FileRepository
{
    /** Loads a record or value object, or null when absent. */
    public function findById(FileId $fileId): ?File;

    /** Persists a new or updated aggregate row. */
    public function create(File $file): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(FileId $fileId): void;

    /** @return list<File> */
    public function findVersions(FileNamespace $fileNamespace, FileName $fileName): array;

    /** Returns the newest revision matching the key. */
    public function latestVersion(FileNamespace $fileNamespace, FileName $fileName): ?File;

    /** Computes the next sequence or version value. */
    public function nextVersionNumber(FileNamespace $fileNamespace, FileName $fileName): int;
}
