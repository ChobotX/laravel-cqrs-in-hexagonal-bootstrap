<?php

declare(strict_types=1);

namespace App\Domain\File\Contract;

use App\Domain\File\FileNamespace;
use App\Domain\File\FileVersion;
use App\Domain\File\StoragePath;
use DateTimeImmutable;

final readonly class File
{
    public function __construct(
        public FileId $id,
        public FileNamespace $namespace,
        public FileName $originalName,
        public StoragePath $storagePath,
        public MimeType $mimeType,
        public int $sizeInBytes,
        public FileVersion $version,
        public string $uploadedBy,
        public DateTimeImmutable $uploadedAt,
    ) {}
}
