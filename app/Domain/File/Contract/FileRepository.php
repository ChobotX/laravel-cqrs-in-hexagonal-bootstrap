<?php

declare(strict_types=1);

namespace App\Domain\File\Contract;

use App\Domain\File\FileNamespace;

interface FileRepository
{
    public function findById(FileId $fileId): ?File;

    public function create(File $file): void;

    public function delete(FileId $fileId): void;

    /** @return list<File> */
    public function findVersions(FileNamespace $fileNamespace, FileName $fileName): array;

    public function latestVersion(FileNamespace $fileNamespace, FileName $fileName): ?File;

    public function nextVersionNumber(FileNamespace $fileNamespace, FileName $fileName): int;
}
