<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Repository;

use App\Domain\File\Contract\Entity\File;
use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\File\Contract\ValueObject\FileName;
use App\Domain\File\ValueObject\FileNamespace;

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
